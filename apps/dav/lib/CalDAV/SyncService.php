<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV;

use OCP\AppFramework\Db\TTransactional;
use OCP\AppFramework\Http;
use OCP\DB\Exception as DbException;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IDBConnection;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;
use Sabre\DAV\Xml\Response\MultiStatus;
use Sabre\DAV\Xml\Service as SabreXmlService;

class SyncService {
	use TTransactional;

	public function __construct(
		private readonly CalDavBackend $backend,
		private readonly IDBConnection $dbConnection,
		private readonly IClientService $clientService,
		private readonly IConfig $config,
		private readonly LoggerInterface $logger,
	) {
	}

	public function syncRemoteCalendar(
		string $url,
		string $username,
		string $calendarUrl,
		string $sharedSecret,
		?string $syncToken,
		string $targetCalendarHash,
		string $targetPrincipal,
		array $targetProperties,
	): string {
		$calendar = $this->ensureCalendarExists($targetPrincipal, $targetCalendarHash, $targetProperties);
		$calendarId = $calendar['id'];

		try {
			$response = $this->requestSyncReport($url, $username, $calendarUrl, $sharedSecret, $syncToken);
		} catch (ClientExceptionInterface $ex) {
			if ($ex->getCode() === Http::STATUS_UNAUTHORIZED) {
				// Remote server revoked access to the calendar => remove it
				$this->backend->deleteCalendar($calendarId);
				$this->logger->error('Authorization failed, remove address book: ' . $url, ['app' => 'dav']);
				throw $ex;
			}
			$this->logger->error('Client exception:', ['app' => 'dav', 'exception' => $ex]);
			throw $ex;
		}

		foreach ($response['response'] as $resource => $status) {
			$objectUri = basename($resource);
			if (isset($status[200])) {
				$vCard = $this->download($url, $username, $sharedSecret, $resource);
				$this->atomic(function () use ($calendarId, $objectUri, $vCard): void {
					$existingCard = $this->backend->getCalendarObject($calendarId, $objectUri);
					if ($existingCard === false) {
						$this->backend->createCalendarObject($calendarId, $objectUri, $vCard);
					} else {
						$this->backend->createCalendarObject($calendarId, $objectUri, $vCard);
					}
				}, $this->dbConnection);
			} else {
				$this->backend->deleteCalendarObject($calendarId, $objectUri);
			}
		}

		return $response['token'];
	}

	/**
	 * @throws DbException If creating the calendar fails
	 */
	public function ensureCalendarExists(string $principal, string $uri, array $properties): ?array {
		try {
			return $this->atomic(function () use ($principal, $uri, $properties) {
				$calendar = $this->backend->getCalendarByUri($principal, $uri);
				if (!is_null($calendar)) {
					return $calendar;
				}
				$this->backend->createCalendar($principal, $uri, $properties);

				return $this->backend->getCalendarByUri($principal, $uri);
			}, $this->dbConnection);
		} catch (DbException $e) {
			// READ COMMITTED doesn't prevent a nonrepeatable read above, so
			// two processes might create an address book here. Ignore our
			// failure and continue loading the entry written by the other process
			if ($e->getReason() !== DbException::REASON_UNIQUE_CONSTRAINT_VIOLATION) {
				throw $e;
			}

			// If this fails we might have hit a replication node that does not
			// have the row written in the other process.
			// TODO: find an elegant way to handle this
			$calendar = $this->backend->getCalendarByUri($principal, $uri);
			if ($calendar === null) {
				throw new DbException(
					'Could not create calendar: ' . $e->getMessage(),
					$e->getCode(),
					$e,
				);
			}

			return $calendar;
		}
	}

	/**
	 * TODO: merge with CardDav/SynService?
	 */
	protected function requestSyncReport(string $url, string $userName, string $addressBookUrl, string $sharedSecret, ?string $syncToken): array {
		$client = $this->clientService->newClient();
		$uri = $this->prepareUri($url, $addressBookUrl);

		$options = [
			'auth' => [$userName, $sharedSecret],
			'body' => $this->buildSyncCollectionRequestBody($syncToken),
			'headers' => ['Content-Type' => 'application/xml', 'Cookie' => 'XDEBUG_SESSION=XDEBUG_ECLIPSE;'],
			'timeout' => $this->config->getSystemValueInt('caldav_sync_request_timeout', IClient::DEFAULT_REQUEST_TIMEOUT)
		];

		$response = $client->request(
			'REPORT',
			$uri,
			$options
		);

		$body = $response->getBody();
		assert(is_string($body));

		return $this->parseMultiStatus($body);
	}

	/**
	 * TODO: merge with CardDav/SynService?
	 */
	private function buildSyncCollectionRequestBody(?string $syncToken): string {
		$dom = new \DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;
		$root = $dom->createElementNS('DAV:', 'd:sync-collection');
		$sync = $dom->createElement('d:sync-token', $syncToken ?? '');
		$prop = $dom->createElement('d:prop');
		$cont = $dom->createElement('d:getcontenttype');
		$etag = $dom->createElement('d:getetag');

		$prop->appendChild($cont);
		$prop->appendChild($etag);
		$root->appendChild($sync);
		$root->appendChild($prop);
		$dom->appendChild($root);
		return $dom->saveXML();
	}

	/**
	 * TODO: merge with CardDav/SynService?
	 */
	private function parseMultiStatus($body) {
		$xml = new SabreXmlService();

		/** @var MultiStatus $multiStatus */
		$multiStatus = $xml->expect('{DAV:}multistatus', $body);

		$result = [];
		foreach ($multiStatus->getResponses() as $response) {
			$result[$response->getHref()] = $response->getResponseProperties();
		}

		return ['response' => $result, 'token' => $multiStatus->getSyncToken()];
	}

	/**
	 * TODO: merge with CardDav/SynService?
	 */
	private function prepareUri(string $host, string $path): string {
		/*
		 * The trailing slash is important for merging the uris together.
		 *
		 * $host is stored in oc_trusted_servers.url and usually without a trailing slash.
		 *
		 * Example for a report request
		 *
		 * $host = 'https://server.internal/cloud'
		 * $path = 'remote.php/dav/addressbooks/system/system/system'
		 *
		 * Without the trailing slash, the webroot is missing:
		 * https://server.internal/remote.php/dav/addressbooks/system/system/system
		 *
		 * Example for a download request
		 *
		 * $host = 'https://server.internal/cloud'
		 * $path = '/cloud/remote.php/dav/addressbooks/system/system/system/Database:alice.vcf'
		 *
		 * The response from the remote usually contains the webroot already and must be normalized to:
		 * https://server.internal/cloud/remote.php/dav/addressbooks/system/system/system/Database:alice.vcf
		 */
		$host = rtrim($host, '/') . '/';

		$uri = \GuzzleHttp\Psr7\UriResolver::resolve(
			\GuzzleHttp\Psr7\Utils::uriFor($host),
			\GuzzleHttp\Psr7\Utils::uriFor($path)
		);

		return (string)$uri;
	}

	/**
	 * TODO: merge with CardDav/SynService?
	 */
	protected function download(string $url, string $userName, string $sharedSecret, string $resourcePath): string {
		$client = $this->clientService->newClient();
		$uri = $this->prepareUri($url, $resourcePath);

		$options = [
			'auth' => [$userName, $sharedSecret],
		];

		$response = $client->get(
			$uri,
			$options
		);

		return (string)$response->getBody();
	}
}
