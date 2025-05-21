<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\CalDavBackend;
use OCA\Mail\Vendor\Psr\Log\LoggerInterface;
use OCP\AppFramework\Http;
use OCP\Federation\Exceptions\ProviderCouldNotAddShareException;
use OCP\Federation\ICloudFederationProvider;
use OCP\Federation\ICloudFederationShare;

class CalendarFederationProvider implements ICloudFederationProvider {
	public const SHARE_TYPE = 'calendar';

	public function __construct(
		private readonly CalDavBackend $calDavBackend,
		private readonly LoggerInterface $logger,
	) {
	}

	public function getShareType(): string {
		return self::SHARE_TYPE;
	}

	public function shareReceived(ICloudFederationShare $share): string {
		if (!$this->isFederationEnabled()) {
			$this->logger->debug('Received a federation invite but federation is disabled');
			throw new ProviderCouldNotAddShareException('Server does not support talk federation', '', Http::STATUS_SERVICE_UNAVAILABLE);
		}
		if (!in_array($share->getShareType(), $this->getSupportedShareTypes(), true)) {
			$this->logger->debug('Received a federation invite for invalid share type');
			throw new ProviderCouldNotAddShareException('Support for sharing with non-users not implemented yet', '', Http::STATUS_NOT_IMPLEMENTED);
			// TODO: Implement group shares
		}

		$protocol = $share->getProtocol();
		$calendarUri = $protocol['uri'];
		$displayName = $protocol['displayName'];
		$remoteBase = $protocol['remoteBase'];

		if (!$calendarUri || !$displayName || !$remoteBase) {
			throw new ProviderCouldNotAddShareException('Required request data not found', '', Http::STATUS_BAD_REQUEST);
		}

		$principal = 'principals/users/' . $share->getShareWith();
		$existingCalendar = $this->calDavBackend->getCalendarByUri($principal, $calendarUri);
		if ($existingCalendar) {
			throw new ProviderCouldNotAddShareException('Calendar has already been shared with the user', '', Http::STATUS_CONFLICT);
		}

		$calendarId = $this->calDavBackend->createCalendar($principal, $calendarUri, [
			CalDavBackend::PROP_FEDERATED => 'true',
			CalDavBackend::PROP_FEDERATION_TOKEN => $share->getShareSecret(),
			CalDavBackend::PROP_FEDERATION_REMOTE => $remoteBase,
			'{DAV:}displayname' => $displayName,
		]);
		return (string)$calendarId;
	}

	public function notificationReceived($notificationType, $providerId, array $notification) {
		// TODO: Implement notificationReceived() method.
	}

	/**
	 * @return string[]
	 */
	public function getSupportedShareTypes(): array {
		return ['user'];
	}

	private function isFederationEnabled(): bool {
		// FIXME: implement this correctly
		return true;
	}
}
