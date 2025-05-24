<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCP\AppFramework\Db\Entity;
use OCP\DB\Types;

// TODO: write a migration for this entity

/**
 * @method string getPrincipaluri()
 * @method void setPrincipaluri(string $principaluri)
 * @method string getUri()
 * @method void setUri(string $uri)
 * @method string getDisplayName()
 * @method void setDisplayName(string $displayName)
 * @method string|null getColor()
 * @method void setColor(string|null $color)
 * @method int getPermissions()
 * @method void setPermissions(int $permissions)
 * @method int getSyncToken()
 * @method void setSyncToken(int $syncToken)
 * @method string getRemoteUrl()
 * @method void setRemoteUrl(string $remoteUrl)
 * @method string getToken()
 * @method void setToken(string $token)
 * @method int|null getLastSync()
 * @method void setLastSync(int|null $lastSync)
 * @method string getSharedBy()
 * @method void setSharedBy(string $sharedBy)
 * @method string getSharedByDisplayName()
 * @method void setSharedByDisplayName(string $sharedByDisplayName)
 */
class FederatedCalendarEntity extends Entity {
	protected string $principaluri = '';
	protected string $uri = '';
	protected string $displayName = '';
	protected ?string $color = null;
	protected int $permissions = 0;
	protected int $syncToken = 0;
	protected string $remoteUrl = '';
	protected string $token = '';
	protected ?int $lastSync = null;
	protected string $sharedBy = '';
	protected string $sharedByDisplayName = '';

	public function __construct() {
		$this->addType('principaluri', Types::STRING);
		$this->addType('uri', Types::STRING);
		$this->addType('color', Types::STRING);
		$this->addType('displayName', Types::STRING);
		$this->addType('permissions', Types::INTEGER);
		$this->addType('syncToken', Types::INTEGER);
		$this->addType('remoteUrl', Types::STRING);
		$this->addType('token', Types::STRING);
		$this->addType('lastSync', Types::INTEGER);
		$this->addType('sharedBy', Types::STRING);
		$this->addType('sharedByDisplayName', Types::STRING);
	}

	public function toFederatedCalendar(): FederatedCalendar {
		return new FederatedCalendar(
			$this->getId(),
			$this->getUri(),
			$this->getDisplayName(),
			$this->getColor(),
			$this->getPermissions(),
			$this->getPrincipaluri(),
		);
	}

	public function getSyncTokenForSabre(): string {
		return 'http://sabre.io/ns/sync/' . $this->getSyncToken();
	}

	public function getSharedByPrincipal(): string {
		return 'principals/remote-users/' . base64_encode($this->getSharedBy());
	}
}
