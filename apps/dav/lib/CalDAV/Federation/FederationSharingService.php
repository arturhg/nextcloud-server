<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\Calendar;
use OCA\DAV\DAV\Sharing\IShareable;
use OCP\AppFramework\Http;
use OCP\Federation\ICloudFederationFactory;
use OCP\Federation\ICloudFederationProviderManager;
use OCP\IURLGenerator;
use OCP\IUserManager;

class FederationSharingService {
	public function __construct(
		private readonly ICloudFederationProviderManager $federationManager,
		private readonly ICloudFederationFactory $federationFactory,
		private readonly IUserManager $userManager,
		//private readonly CalDavBackend $calDavBackend,
		private readonly IURLGenerator $url,
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

		$token = 'foodbabe'; // TODO: where to get this token from?
		$share = $this->federationFactory->getCloudFederationShare(
			$shareWith,
			$shareable->getName(),
			'calendar',
			CalendarFederationProvider::SHARE_TYPE,
			// TODO: owner !== sharedBy ???
			$owner->getCloudId(),
			$owner->getDisplayName(),
			$owner->getCloudId(),
			$owner->getDisplayName(),
			$token,
			'user', // TODO: do not hard-code
			'calendar', // TODO: move to constant
		);

		// Evil hack
		/** @var Calendar $calendar */
		$calendar = $shareable;

		// TODO: convert protocol to a DTO
		$protocol = $share->getProtocol();
		$protocol['uri'] = $calendar->getName();
		$protocol['displayName'] = $calendar->getProperties(['{DAV:}displayname'])['{DAV:}displayname'];
		$protocol['remoteBase'] = sprintf(
			'%s/remote.php/dav/calendars/%s',
			$this->url->getWebroot(),
			$owner->getUID(),
		);
		$share->setProtocol($protocol);

		$response = $this->federationManager->sendCloudShare($share);
		if ($response->getStatusCode() === Http::STATUS_CREATED) {
		}
	}
}
