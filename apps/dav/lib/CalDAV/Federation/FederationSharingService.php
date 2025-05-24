<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\Federation\Protocol\CalendarFederationProtocolV1;
use OCA\DAV\DAV\Sharing\IShareable;
use OCP\AppFramework\Http;
use OCP\Federation\ICloudFederationFactory;
use OCP\Federation\ICloudFederationProviderManager;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\OCM\Exceptions\OCMProviderException;
use OCP\Security\ISecureRandom;
use Psr\Log\LoggerInterface;
use Sabre\CalDAV\Calendar;

class FederationSharingService {
	public function __construct(
		private readonly ICloudFederationProviderManager $federationManager,
		private readonly ICloudFederationFactory $federationFactory,
		private readonly IUserManager $userManager,
		private readonly IURLGenerator $url,
		private readonly LoggerInterface $logger,
		private readonly ISecureRandom $random,
	) {
	}

	private function convertPrincipalToFederatedId(string $principal): ?string {
		[$prefix, $collection, $encodedId] = explode('/', $principal);
		if ($prefix !== 'principals' && $collection !== 'remote') {
			return null;
		}

		return urldecode($encodedId);
	}

	public function shareWith(IShareable $shareable, string $principal, int $access): void {
		$shareWith = $this->convertPrincipalToFederatedId($principal);
		if (!$shareWith) {
			throw new \Exception('Principal is not belonging to a remote user');
		}

		[,, $ownerUid] = explode('/', $shareable->getOwner());
		$owner = $this->userManager->get($ownerUid);
		if (!$owner) {
			throw new \Exception('Shareable is not owned by a user on this server');
		}

		// Need a calendar instance to extract properties for the protocol
		$calendar = $shareable;
		if (!($calendar instanceof Calendar)) {
			throw new \RuntimeException('Shareable is not a calendar');
		}

		$getProp = static fn (Calendar $calendar, string $prop) =>
			$calendar->getProperties([$prop])[$prop] ?? null;

		$displayName = $getProp($calendar, '{DAV:}displayname') ?? '';

		$token = $this->random->generate(32);
		$share = $this->federationFactory->getCloudFederationShare(
			$shareWith,
			$shareable->getName(),
			$displayName,
			CalendarFederationProvider::SHARE_TYPE,
			// TODO: owner !== sharedBy ???
			$owner->getCloudId(),
			$owner->getDisplayName(),
			$owner->getCloudId(),
			$owner->getDisplayName(),
			$token,
			'user', // TODO: do not hard-code
			CalendarFederationProvider::CALENDAR_RESOURCE,
		);

		$calendarUrl = $this->url->linkTo('', 'remote.php') . "/dav/calendars/$ownerUid/" . $calendar->getName();
		$calendarUrl = $this->url->getAbsoluteURL($calendarUrl);
		$protocol = new CalendarFederationProtocolV1();
		$protocol->setUrl($calendarUrl);
		$protocol->setDisplayName($displayName);
		$protocol->setColor($getProp($calendar, '{http://apple.com/ns/ical/}calendar-color'));
		$protocol->setAccess($access);
		$share->setProtocol([
			// Preserve original protocol contents
			...$share->getProtocol(),
			...$protocol->toProtocol(),
		]);

		try {
			$response = $this->federationManager->sendCloudShare($share);
		} catch (OCMProviderException $e) {
			$this->logger->error('Failed to create federated calendar share: ' . $e->getMessage(), [
				'exception' => $e,
			]);
			throw $e;
		}

		if ($response->getStatusCode() !== Http::STATUS_CREATED) {
			$this->logger->error('Failed to create federated calendar share: Server replied with code ' . $response->getStatusCode(), [
				'responseBody' => $response->getBody(),
			]);
		}
	}
}
