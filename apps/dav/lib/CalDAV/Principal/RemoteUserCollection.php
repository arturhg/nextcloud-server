<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Principal;

use OCA\DAV\DAV\RemoteUserPrincipalBackend;
use OCA\DAV\DAV\Sharing\SharingMapper;
use OCP\Server;
use Sabre\DAV\SimpleCollection;

class RemoteUserCollection extends SimpleCollection {
	private readonly SharingMapper $sharingMapper;

	/*
	private array $cachedChildren = [];
	private bool $hasCachedAll;
	*/

	public function __construct(
		private readonly RemoteUserPrincipalBackend $principalBackend,
	) {
		// TODO: inject
		$this->sharingMapper = Server::get(SharingMapper::class);

		// TODO: make this lazy
		$children = $this->loadChildren();
		parent::__construct('remote-users', $children);
	}

	private function loadChildren(): array {
		// TODO: do we need the resource type here as an argument?
		$rows = $this->sharingMapper->getRemoteUserPrincipalUris('calendar');

		$children = [];
		foreach ($rows as $row) {
			$principalUri = $row['principaluri'];

			[,, $encodedPrincipal] = explode('/', $principalUri);
			[$cloudId, $scope] = explode('|', base64_decode($encodedPrincipal));

			$children[] = new RemoteUser($this->principalBackend, [
				'uri' => $principalUri,
				'{http://nextcloud.com/ns}cloud-id' => $cloudId,
				'{http://nextcloud.com/ns}scope' => $scope,
			]);
		}

		return $children;
	}

	public function getChild($name) {
		$child = parent::getChild($name);
		return $child;
	}

	public function getChildren() {
		$children = parent::getChildren();
		return $children;
	}
}
