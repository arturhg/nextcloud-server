<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

class FederatedCalendarService {
	public function __construct(
		private readonly FederatedCalendarMapper $federatedCalendarsMapper,
	) {
	}

	public function createFederatedCalendar(
		FederatedCalendarImpl $calendar,
		string                $principalUri,
	): void {
		$entity = $this->federatedCalendarToEntity($calendar, $principalUri);
		$this->federatedCalendarsMapper->insert($entity);
	}

	private function federatedCalendarToEntity(
		FederatedCalendarImpl $calendar,
		string                $principalUri,
	): FederatedCalendarEntity {
		$entity = new FederatedCalendarEntity();
		$entity->setPrincipalUri($principalUri);
		$entity->setDisplayName($calendar->getDisplayName());
		$entity->setColor($calendar->getDisplayColor());
		$entity->setPermissions($calendar->getPermissions());
		return $entity;
	}
}
