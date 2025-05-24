<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\CalDAV\Federation;

use OCP\Defaults;
use OCP\Federation\ICloudIdManager;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Sabre\DAV\Auth\Backend\BackendInterface;
use Sabre\HTTP\Auth\Basic as BasicAuth;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

class FederatedCalendarAuth implements BackendInterface {
	public function __construct(
		private readonly IURLGenerator $url,
		private readonly IDBConnection $db,
		private readonly ICloudIdManager $cloudIdManager,
	) {
		//$this->principalPrefix = 'principals/system/';
		$this->principalPrefix = 'principals/users/';

		$defaults = new Defaults();
		$this->realm = $defaults->getName();
	}

	private function validateUserPassForPath(string $path, string $username, string $password): array {
		/*
		if ($username !== 'system') {
			return false;
		}
		*/

		if (!$this->cloudIdManager->isValidCloudId($username)) {
			return [false, ''];
		}

		$username = base64_encode($username);
		$principalUri = "principals/remote-users/$username";

		/*
		if (!str_starts_with($path, $this->url->getWebroot())) {
			return false;
		}

		$path = substr($path, strlen($this->url->getWebroot()));
		$path = ltrim($path, '/');
		*/

		// path: calendars/<uid>/<calendar-uri>
		[$collection, $userId, $calendarUri] = explode('/', $path);
		if ($collection !== 'calendars') {
			return [false, 'Invalid collection'];
		}

		// FIXME: hackity hack
		$qb = $this->db->getQueryBuilder();
		$qb->select($qb->func()->count('*'))
			->from('dav_shares')
			->where($qb->expr()->eq('principaluri', $qb->createNamedParameter($principalUri)))
			->andWhere($qb->expr()->eq('type', $qb->createNamedParameter('calendar')))
			->andWhere($qb->expr()->eq('token', $qb->createNamedParameter($password)));
			//->andWhere($qb->expr()->eq('publicuri', $qb->createNamedParameter($password)));
		$result = $qb->executeQuery();
		$count = (int)$result->fetchOne();
		$result->closeCursor();
		//return $count > 0;
		//return [$count > 0, $userId];
		return [$count > 0, 'admin'];
	}

	public function check(RequestInterface $request, ResponseInterface $response) {
		$auth = new BasicAuth($this->realm, $request, $response);

		$userpass = $auth->getCredentials();
		if (!$userpass) {
			return [false, "No 'Authorization: Basic' header found. Either the client didn't send one, or the server is misconfigured"];
		}
		[$valid, $userId] = $this->validateUserPassForPath($request->getPath(), $userpass[0], $userpass[1]);
		if (!$valid) {
			return [false, 'Username or password was incorrect'];
		}

		//return [true, $this->principalPrefix.$userpass[0]];
		return [true, $this->principalPrefix.$userId];
	}

	public function challenge(RequestInterface $request, ResponseInterface $response) {
		// TODO: Implement challenge() method.
	}
}
