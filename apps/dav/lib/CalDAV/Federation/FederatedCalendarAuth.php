<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\Defaults;
use OCP\IDBConnection;
use Sabre\DAV\Auth\Backend\BackendInterface;
use Sabre\HTTP\Auth\Basic as BasicAuth;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

final class FederatedCalendarAuth implements BackendInterface {
	private readonly string $realm;

	public function __construct(
		private readonly IDBConnection $db,
	) {
		$defaults = new Defaults();
		$this->realm = $defaults->getName();
	}

	/**
	 * @return string|null A principal uri if the credentials are valid and null otherwise.
	 */
	private function validateUserPass(string $username, string $password): ?string {
		/*
		if (!$this->cloudIdManager->isValidCloudId($username)) {
			return null;
		}

		$username = base64_encode($username);
		$principalUri = "principals/remote-users/$username";
		*/

		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('dav_shares')
			->where($qb->expr()->eq(
				'type',
				$qb->createNamedParameter('calendar', IQueryBuilder::PARAM_STR),
				IQueryBuilder::PARAM_STR),
			)
			->andWhere($qb->expr()->eq(
				'token',
				$qb->createNamedParameter($password, IQueryBuilder::PARAM_STR),
				IQueryBuilder::PARAM_STR),
			);
		$result = $qb->executeQuery();
		$rows = $result->fetchAll();
		$result->closeCursor();

		if (count($rows) > 1) {
			// Should not happen but just to be sure
			return null;
		}

		return (string)$rows[0]['principaluri'];
	}

	public function check(RequestInterface $request, ResponseInterface $response): array {
		$auth = new BasicAuth($this->realm, $request, $response);

		$userpass = $auth->getCredentials();
		if (!$userpass) {
			return [false, "No 'Authorization: Basic' header found. Either the client didn't send one, or the server is misconfigured"];
		}
		$principal = $this->validateUserPass($userpass[0], $userpass[1]);
		if ($principal === null) {
			return [false, 'Username or password was incorrect'];
		}

		return [true, $principal];
	}

	public function challenge(RequestInterface $request, ResponseInterface $response): void {
		// No special challenge is needed here
	}
}
