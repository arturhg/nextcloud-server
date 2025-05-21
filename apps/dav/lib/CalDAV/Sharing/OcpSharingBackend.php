<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Sharing;

use OCP\Share\IShare;
use OCP\Share_Backend;

class OcpSharingBackend implements Share_Backend {
	public function isValidSource($itemSource, $uidOwner) {
		return true;
	}

	public function generateTarget($itemSource, $shareWith) {

	}

	public function formatItems($items, $format, $parameters = null) {
		// TODO: Implement formatItems() method.
	}

	public function isShareTypeAllowed($shareType) {
		return $shareType === IShare::TYPE_REMOTE;
	}
}
