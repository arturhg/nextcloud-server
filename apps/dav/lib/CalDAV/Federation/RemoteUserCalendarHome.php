<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use MicrosoftAzure\Storage\Common\Logger;
use OCA\DAV\CalDAV\CalDavBackend;
use OCA\DAV\CalDAV\Calendar;
use OCP\IConfig;
use OCP\IL10N;
use OCP\Server;
use Psr\Log\LoggerInterface;
use Sabre\CalDAV\Backend;
use Sabre\CalDAV\CalendarHome;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\Node;

class RemoteUserCalendarHome extends CalendarHome {
	//private CalDavBackend $calDavBackend;
	private readonly IL10N $l10n;
	private readonly IConfig $config;
	private readonly LoggerInterface $logger;

	public function __construct(Backend\BackendInterface $caldavBackend, $principalInfo) {
		parent::__construct($caldavBackend, $principalInfo);

		//$this->calDavBackend = $caldavBackend;
		$this->l10n = Server::get(IL10N::class);
		$this->config = Server::get(IConfig::class);
		$this->logger = Server::get(LoggerInterface::class);
	}

	public function getChild($name) {
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

		$shares = $this->caldavBackend->getSharesByShareePrincipal($this->principalInfo['uri']);

		$children = [];
		foreach ($shares as $share) {
			if ($share['type'] !== 'calendar') {
				continue;
			}

			$calendar = $this->caldavBackend->getCalendarById($share['resourceid']);
			$children[] = new Calendar(
				$this->caldavBackend,
				$calendar,
				$this->l10n,
				$this->config,
				$this->logger,
			);
		}

		return $children;
	}
}
