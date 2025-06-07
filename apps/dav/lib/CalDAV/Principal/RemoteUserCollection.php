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
use Sabre\DAVACL\PrincipalCollection;

class RemoteUserCollection extends SimpleCollection {
	private readonly SharingMapper $sharingMapper;

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
		// TODO: do we need the resource type here?
		$rows = $this->sharingMapper->getRemoteUserPrincipalUris('calendar');

		$childrenByCloudId = [];

		foreach ($rows as $row) {
			$principalUri = $row['principaluri'];
			[,, $encodedCloudId, $encodedScope] = explode('/', $principalUri);
			if (!isset($childrenByCloudId[$encodedCloudId])) {
				//$byCloudId[$encodedCloudId] = [];
				$childrenByCloudId[$encodedCloudId] = new SimpleCollection($encodedCloudId, []);
				/*
				$childrenByCloudId[$encodedCloudId] = new PrincipalCollection(
					$this->principalBackend,
				);
				*/
			}

			$childrenByCloudId[$encodedCloudId]->addChild(new RemoteUser($this->principalBackend, [
				'uri' => $principalUri,
				'{http://nextcloud.com/ns}cloud-id' => base64_decode($encodedCloudId),
				'{http://nextcloud.com/ns}scope' => base64_decode($encodedScope),
			]));
		}

		return array_values($childrenByCloudId);
	}
}
