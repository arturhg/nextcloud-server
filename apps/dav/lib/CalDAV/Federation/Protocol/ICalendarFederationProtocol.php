<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation\Protocol;

interface ICalendarFederationProtocol {
	public const PROP_VERSION = 'version';

	public function getVersion(): string;
	public function toProtocol(): array;
}
