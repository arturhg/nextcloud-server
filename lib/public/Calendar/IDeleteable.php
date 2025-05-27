<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCP\Calendar;

use OCP\Calendar\ICalendar;

interface IDeleteable extends ICalendar {
	public function delete(): void;
}
