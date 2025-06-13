<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use MicrosoftAzure\Storage\Common\Logger;
use OCA\DAV\AppInfo\Application;
use OCA\DAV\CalDAV\CalDavBackend;
use OCA\DAV\CalDAV\Calendar;
use OCP\IConfig;
use OCP\IL10N;
use OCP\L10N\IFactory as IL10NFactory;
use OCP\Server;
use Psr\Log\LoggerInterface;
use Sabre\CalDAV\Backend;
use Sabre\CalDAV\CalendarHome;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\Node;

class RemoteUserCalendarHome extends CalendarHome {
	private readonly IL10N $l10n;
	private readonly IConfig $config;
	private readonly LoggerInterface $logger;
	private readonly FederatedCalendarMapper $federatedCalendarMapper;

	public function __construct(Backend\BackendInterface $caldavBackend, $principalInfo) {
		parent::__construct($caldavBackend, $principalInfo);

		// TODO: inject?
		$this->l10n = Server::get(IL10NFactory::class)->get(Application::APP_ID);
		$this->config = Server::get(IConfig::class);
		$this->logger = Server::get(LoggerInterface::class);
		$this->federatedCalendarMapper = Server::get(FederatedCalendarMapper::class);
	}

	public function getChild($name) {
		// Usually, this should be optimized, but we can simply query all children here.
		// Shares are selected by the sharee's principal, so there will be exactly one row for each
		// calendar from this instance which has been shared with that particular remote user.
		// Unless many calendars from this instance are shared with a single remote user, this
		// should never be a performance concern.

		/** @var Node[] $children */
		$children = $this->getChildren();
		foreach ($children as $child) {
			if ($child->getName() === $name) {
				return $child;
			}
		}


		throw new NotFound("Node with name $name could not be found");
	}

	public function getChildren() {
		if (!($this->caldavBackend instanceof CalDavBackend)) {
			return [];
		}

		$children = [];

		$shares = $this->caldavBackend->getSharesByShareePrincipal($this->principalInfo['uri']);
		foreach ($shares as $share) {
			// Type Should always be "calendar" as the sharing service is scoped to calendars.
			// Just to be sure ...
			if ($share['type'] !== 'calendar') {
				continue;
			}

			$calendar = $this->caldavBackend->getCalendarById($share['resourceid']);
			// TODO: implement read-write sharing
			$calendar['{' . \OCA\DAV\DAV\Sharing\Plugin::NS_OWNCLOUD . '}read-only'] = 1;
			$calendar['{' . \OCA\DAV\DAV\Sharing\Plugin::NS_OWNCLOUD . '}owner-principal'] = $this->principalInfo['uri'];
			$children[] = new FederatedCalendar(
				$this->caldavBackend,
				$calendar,
				$this->l10n,
				$this->config,
				$this->logger,
				$this->federatedCalendarMapper,
			);
		}

		return $children;
	}
}
