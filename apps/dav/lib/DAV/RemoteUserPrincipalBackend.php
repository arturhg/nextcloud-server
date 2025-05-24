<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\DAV;

use OCP\Federation\ICloudIdManager;
use OCP\Server;
use Sabre\DAVACL\PrincipalBackend\BackendInterface;

class RemoteUserPrincipalBackend implements BackendInterface {
	public const PRINCIPAL_PREFIX = 'principals/remote-users';

	private readonly ICloudIdManager $cloudIdManager;

	public function __construct() {
		// TODO: inject
		$this->cloudIdManager = Server::get(ICloudIdManager::class);
	}

	public function getPrincipalsByPrefix($prefixPath) {
		// We don't know about all the remote principals on all remote instances
		return [];
	}

	public function getPrincipalByPath($path) {
		if (!str_starts_with($path, self::PRINCIPAL_PREFIX)) {
			return null;
		}

		[,, $encodedCloudId] = explode('/', $path);
		try {
			$cloudId = $this->cloudIdManager->resolveCloudId(base64_decode($encodedCloudId));
		} catch (\InvalidArgumentException $e) {
			return null;
		}

		return [
			'uri' => $path,
			'{DAV:}displayname' => $cloudId->getDisplayId(),
			//'{http://nextcloud.com/ns}federated-by' => $cloudId->getId(),
		];
	}

	public function updatePrincipal($path, \Sabre\DAV\PropPatch $propPatch) {
		throw new \Sabre\DAV\Exception('Updating remote user principal is not supported');
	}

	public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof') {
		return [];
	}

	public function findByUri($uri, $principalPrefix) {
		// We don't know about all the remote principals on all remote instances
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
		throw new \Sabre\DAV\Exception('Setting members of the group is not supported yet');
	}
}
