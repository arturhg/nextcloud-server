<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\Calendar;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use Sabre\CalDAV\Backend\BackendInterface;
use Sabre\DAV\Sharing\ISharedNode;
use Sabre\DAV\Sharing\Plugin as SabreSharingPlugin;
use Sabre\DAV\Xml\Element\Sharee;

class FederatedCalendar extends Calendar implements ISharedNode {
	public const PROPERTY_CALENDAR_UID = '{NC:}uid';

	private string $uid;

	public function __construct(BackendInterface $caldavBackend, $calendarInfo, IL10N $l10n, IConfig $config, LoggerInterface $logger) {
		parent::__construct($caldavBackend, $calendarInfo, $l10n, $config, $logger);

		$this->uid = $calendarInfo[self::PROPERTY_CALENDAR_UID];
	}


	public function getShareAccess(): int {
		return SabreSharingPlugin::ACCESS_NOTSHARED;
	}

	public function getShareResourceUri(): string {
		return 'urn:uid:' . $this->uid;
	}

	/**
	 * @param \Sabre\DAV\Xml\Element\Sharee[] $sharees
	 */
	public function updateInvites(array $sharees): void {
		// TODO: Implement updateInvites() method.
	}

	/**
	 * @return \Sabre\DAV\Xml\Element\Sharee[]
	 */
	public function getInvites(): array {
		$sharees = [];
		$sharees[] = new Sharee([
			'href' => '/federated/foreign.com/users/userid',
			'access' => SabreSharingPlugin::ACCESS_READ,
			'comment' => '',
			'inviteStatus' => SabreSharingPlugin::INVITE_ACCEPTED,
		]);
		return $sharees;
	}
}
