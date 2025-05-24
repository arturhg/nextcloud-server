<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCA\DAV\CalDAV\CalDavBackend;
use OCA\DAV\DAV\Sharing\Backend;
use OCP\Calendar\ICalendar;
use OCP\Calendar\ICalendarIsShared;
use OCP\Calendar\ICalendarIsWritable;
use OCP\Calendar\IDeleteable;
use OCP\Constants;
use OCP\Server;

class FederatedCalendar implements ICalendar, ICalendarIsShared, ICalendarIsWritable, IDeleteable {
	public function __construct(
		private int $id,
		private string $uri,
		private string $displayName,
		private ?string $color,
		private int $permissions,
		private string $principalUri,
	) {
	}

	public function getKey(): string {
		return (string)$this->id;
	}

	public function getUri(): string {
		return $this->uri;
	}

	public function getDisplayName(): ?string {
		return $this->displayName;
	}

	public function getDisplayColor(): ?string {
		return $this->color;
	}

	public function search(string $pattern, array $searchProperties = [], array $options = [], ?int $limit = null, ?int $offset = null): array {
		// TODO: Implement search() method.
		//return [];
		$calDavBackend = Server::get(CalDavBackend::class);
		$calendarInfo = [
			'id' => $this->id,
			'principaluri' => $this->principalUri,
			'federated' => true,
			'{http://owncloud.org/ns}owner-principal' => $this->principalUri,
		];
		$result = $calDavBackend->search($calendarInfo, $pattern, $searchProperties, $options, $limit, $offset);

		foreach ($result as $object) {
			assert(count($object['objects']) === 1);
		}

		$objects = array_map(static fn ($result) => $result['objects'][0], $result);
		//$objects = array_map(static fn ($result) => ['VEVENT' => $result['objects'][0]], $result);
		//return array_merge(...$objects);
		return $objects;
	}

	public function getPermissions(): int {
		// TODO: implement this properly via ACLs?
		return Constants::PERMISSION_READ;
	}

	public function isDeleted(): bool {
		return false;
	}

	public function isShared(): bool {
		return true;
	}

	public function isWritable(): bool {
		return false;
	}

	public function delete(): void {
		$federatedCalendarMapper = Server::get(FederatedCalendarMapper::class);
		$federatedCalendarMapper->deleteById($this->id);
	}
}
