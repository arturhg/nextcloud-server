<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Principal;

use OCA\DAV\DAV\RemoteUserPrincipalBackend;
use OCA\DAV\DAV\Sharing\SharingMapper;
use Sabre\DAV\Exception\NotFound;

class RemoteUserCollection extends \Sabre\DAV\Collection {
	private bool $hasCachedAllChildren = false;

	/** @var RemoteUser[] */
	private array $children = [];

	/** @var array<string, RemoteUser|null> */
	private array $childrenByName = [];

	public function __construct(
		private readonly RemoteUserPrincipalBackend $principalBackend,
		private readonly SharingMapper $sharingMapper,
	) {
	}

	public function getName() {
		return 'remote-users';
	}

	/**
	 * @psalm-return list<string, string> [$cloudId, $scope]
	 */
	private function decodeRemoteUserPrincipalUri(string $principalUri): array {
		[,, $encodedPrincipal] = explode('/', $principalUri);
		[$cloudId, $scope] = explode('|', base64_decode($encodedPrincipal));
		return [$cloudId, $scope];
	}

	private function rowToRemoteUser(string $principalUri): RemoteUser {
		[$cloudId, $scope] = $this->decodeRemoteUserPrincipalUri($principalUri);
		return new RemoteUser($this->principalBackend, [
			'uri' => $principalUri,
			'{http://nextcloud.com/ns}cloud-id' => $cloudId,
			'{http://nextcloud.com/ns}scope' => $scope,
		]);
	}

	private function loadChildren(): array {
		$rows = $this->sharingMapper->getRemoteUserPrincipalUris('calendar');
		return array_map(fn (array $row) => $this->rowToRemoteUser($row['principaluri']), $rows);
	}

	public function getChild($name) {
		if (isset($this->childrenByName[$name])) {
			$child = $this->childrenByName[$name];
			if ($child === null) {
				throw new NotFound("Principal not found: $name");
			}

			return $child;
		}

		foreach ($this->children as $child) {
			if ($child->getName() === $name) {
				$this->childrenByName[$name] = $child;
				return $child;
			}
		}

		// TODO: check the following claim
		// It makes sense to load and cache only a single principal here as there are two main usage
		// patterns: Either all principals are loaded via getChildren() (when listing) or a single,
		// specific one is requested by path.
		$principalUri = "principals/remote-users/$name";
		if ($this->sharingMapper->hasRemoteUserPrincipalUri('calendar', $principalUri)) {
			$remoteUser = $this->rowToRemoteUser($principalUri);
			$this->childrenByName[$name] = $remoteUser;
			return $remoteUser;
		}

		// Skip next search
		$this->childrenByName[$name] = null;
		throw new NotFound();
	}

	public function getChildren() {
		if (!$this->hasCachedAllChildren) {
			$this->children = $this->loadChildren();
			$this->hasCachedAllChildren = true;
		}

		return $this->children;
	}
}
