<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\Federation\Protocol\CalendarFederationProtocolV1;
use OCA\DAV\DAV\Sharing\IShareable;
use OCA\DAV\DAV\Sharing\SharingMapper;
use OCP\AppFramework\Http;
use OCP\Federation\ICloudFederationFactory;
use OCP\Federation\ICloudFederationProviderManager;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\OCM\Exceptions\OCMProviderException;
use OCP\Security\ISecureRandom;
use Psr\Log\LoggerInterface;
use Sabre\CalDAV\Calendar;

// TODO: needs to be abstract like the addressbook/calendar sharing service
class FederationSharingService {
	public function __construct(
		private readonly ICloudFederationProviderManager $federationManager,
		private readonly ICloudFederationFactory $federationFactory,
		private readonly IUserManager $userManager,
		private readonly IURLGenerator $url,
		private readonly LoggerInterface $logger,
		private readonly ISecureRandom $random,
		private readonly SharingMapper $sharingMapper,
	) {
	}

	private function decodeRemoteUserPrincipal(string $principal): ?string {
		[$prefix, $collection, $encodedId] = explode('/', $principal);
		if ($prefix !== 'principals' && $collection !== 'remote-users') {
			return null;
		}

		return base64_decode($encodedId);
	}

	public function shareWith(IShareable $shareable, string $principal, int $access): void {
		$shareWith = $this->decodeRemoteUserPrincipal($principal);
		if (!$shareWith) {
			throw new \Exception('Principal of sharee is not belonging to a remote user');
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

		$relativeCalendarUrl = "calendars/$ownerUid/" . $calendar->getName();
		$calendarUrl = $this->url->linkTo('', 'remote.php') . "/dav/$relativeCalendarUrl";
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

		// 1. Send share to federated instance
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
			return;
		}

		// 2. Create a local DAV share to track the auth token
		$scope = base64_encode($relativeCalendarUrl);
		$principal = "$principal/$scope";
		$this->sharingMapper->deleteShare($shareable->getResourceId(), 'calendar', $principal);
		$this->sharingMapper->shareWithToken(
			$shareable->getResourceId(),
			'calendar',
			$access,
			$principal,
			$token,
		);
	}
}
