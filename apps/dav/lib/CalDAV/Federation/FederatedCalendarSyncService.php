<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\SyncService as CalDavSyncService;
use OCP\Federation\ICloudIdManager;
use Psr\Log\LoggerInterface;

class FederatedCalendarSyncService {
	public function __construct(
		private readonly FederatedCalendarMapper $federatedCalendarMapper,
		private readonly LoggerInterface $logger,
		private readonly CalDavSyncService $syncService,
		private readonly ICloudIdManager $cloudIdManager,
	) {
	}

	/**
	 * Pull sync
	 */
	public function syncChunk(int $chunkSize): void {
		$calendars = $this->federatedCalendarMapper->findUnsyncedLimited($chunkSize);
		foreach ($calendars as $calendar) {
			$this->syncOne($calendar);
			$this->federatedCalendarMapper->updateSyncTime($calendar->getId());
		}
	}

	public function syncOne(FederatedCalendarEntity $calendar): void {
		// TODO: do we need to discover here?
		//$endPoints = $this->ocsDiscoveryService->discover($remoteUrl, 'FEDERATED_SHARING');
		//$cardDavUser = $endPoints['carddav-user'] ?? 'system';
		//$addressBookUrl = isset($endPoints['system-address-book']) ? trim($endPoints['system-address-book'], '/') : 'remote.php/dav/addressbooks/system/system/system';

		//$calDavUser = 'calendar-federation';
		//$calDavUser = $calendar->getSharedBy();

		[,, $sharedWith] = explode('/', $calendar->getPrincipaluri());
		$calDavUser = $this->cloudIdManager->getCloudId($sharedWith, null)->getId();
		$remoteUrl = $calendar->getRemoteUrl();
		$syncToken = $calendar->getSyncTokenForSabre();

		try {
			$newSyncToken = $this->syncService->syncRemoteCalendar(
				$remoteUrl,
				$calDavUser,
				$calendar->getToken(),
				$syncToken,
				$calendar,
			);

			$newSyncToken = (int)substr($newSyncToken, strlen('http://sabre.io/ns/sync/'));
			if ($newSyncToken !== $calendar->getSyncToken()) {
				$this->federatedCalendarMapper->updateSyncToken($calendar->getId(), $newSyncToken);
			} else {
				$this->logger->debug("Sync Token for $remoteUrl unchanged from previous sync");
			}
		} catch (\Exception $ex) {
			// TODO: handle errors
		}
	}
}
