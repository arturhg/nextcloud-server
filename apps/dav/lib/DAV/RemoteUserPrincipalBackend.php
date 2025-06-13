<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\DAV;

use OCA\DAV\DAV\Sharing\SharingMapper;
use OCP\Federation\ICloudIdManager;
use Sabre\DAVACL\PrincipalBackend\BackendInterface;

class RemoteUserPrincipalBackend implements BackendInterface {
	public const PRINCIPAL_PREFIX = 'principals/remote-users';

	private bool $hasCachedAllChildren = false;

	/** @var array<string, mixed>[] */
	private array $principals = [];

	/** @var array<string, array<string, mixed>|null> */
	private array $principalsByPath = [];

	public function __construct(
		private readonly ICloudIdManager $cloudIdManager,
		private readonly SharingMapper $sharingMapper,
	) {
	}

	public function getPrincipalsByPrefix($prefixPath) {
		if ($prefixPath !== self::PRINCIPAL_PREFIX) {
			return [];
		}

		if (!$this->hasCachedAllChildren) {
			$this->loadChildren();
			$this->hasCachedAllChildren = true;
		}

		return $this->principals;
	}

	public function getPrincipalByPath($path) {
		[$prefix] = \Sabre\Uri\split($path);
		if ($prefix !== self::PRINCIPAL_PREFIX) {
			return null;
		}

		if (isset($this->principalsByPath[$path])) {
			return $this->principalsByPath[$path];
		}

		// TODO: check the following claim
		// It makes sense to load and cache only a single principal here as there are two main usage
		// patterns: Either all principals are loaded via getChildren() (when listing) or a single,
		// specific one is requested by path.
		if (!$this->sharingMapper->hasShareWithPrincipalUri('calendar', $path)) {
			$this->principalsByPath[$path] = null;
			return null;
		}

		$principal = $this->principalUriToPrincipal($path);
		$this->principalsByPath[$path] = $principal;
		return $principal;
	}

	public function updatePrincipal($path, \Sabre\DAV\PropPatch $propPatch) {
		throw new \Sabre\DAV\Exception('Updating remote user principal is not supported');
	}

	public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof') {
		return [];
	}

	public function findByUri($uri, $principalPrefix) {
		if (str_starts_with($uri, 'principal:')) {
			$principal = substr($uri, strlen('principal:'));
			$principal = $this->getPrincipalByPath($principal);
			if ($principal !== null) {
				return $principal['uri'];
			}
		}

		return null;
	}

	public function getGroupMemberSet($principal) {
		return [];
	}

	public function getGroupMembership($principal) {
		// TODO: for now the group principal has only one member, the user itself
		$principal = $this->getPrincipalByPath($principal);
		if (!$principal) {
			throw new \Sabre\DAV\Exception('Principal not found');
		}

		return [$principal['uri']];
	}

	public function setGroupMemberSet($principal, array $members) {
		throw new \Sabre\DAV\Exception('Adding members to remote user is not supported');
	}

	/**
	 * @psalm-return list<string, string> [$cloudId, $scope]
	 */
	private function decodeRemoteUserPrincipalName(string $name): array {
		[$cloudId, $scope] = explode('|', base64_decode($name));
		return [$cloudId, $scope];
	}

	/**
	 * @return array<string, array>
	 */
	private function principalUriToPrincipal(string $principalUri): array {
		[, $name] = \Sabre\Uri\split($principalUri);
		[$encodedCloudId, $scope] = $this->decodeRemoteUserPrincipalName($name);
		$cloudId = $this->cloudIdManager->resolveCloudId($encodedCloudId);
		return [
			'uri' => $principalUri,
			'{DAV:}displayname' => $cloudId->getDisplayId(),
			'{http://nextcloud.com/ns}cloud-id' => $cloudId,
			'{http://nextcloud.com/ns}federation-scope' => $scope,
		];
	}

	private function loadChildren(): array {
		$rows = $this->sharingMapper->getPrincipalUrisByPrefix('calendar', self::PRINCIPAL_PREFIX);
		$this->principals = array_map(
			fn (array $row) => $this->principalUriToPrincipal($row['principaluri']),
			$rows,
		);

		$this->principalsByPath = [];
		foreach ($this->principals as $child) {
			$this->principalsByPath[$child['uri']] = $child;
		}
	}
}
