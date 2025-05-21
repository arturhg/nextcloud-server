<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Federation\Command;

use OCA\Federation\DbHandler;
use OCA\Federation\SyncFederationCalendars as SyncService;
use OCP\OCS\IDiscoveryService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFederationCalendars extends Command {
	public function __construct(
		private readonly SyncService $syncService,
		private readonly DbHandler $dbHandler,
		private readonly IDiscoveryService $ocsDiscoveryService,
	) {
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('federation:sync-calendars')
			->setDescription('TODO');
	}

	private function findTrustedServer(string $needle): ?array {
		$trustedServers = $this->dbHandler->getAllServer();
		foreach ($trustedServers as $trustedServer) {
			if (str_contains($trustedServer['url'], $needle)) {
				return $trustedServer;
			}
		}

		return null;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$progress = new ProgressBar($output);
		$progress->start();


		$trustedServer = $this->findTrustedServer('fed1');
		if (!$trustedServer) {
			return 1;
		}

		$endPoints = $this->ocsDiscoveryService->discover($trustedServer['url'], 'FEDERATED_SHARING', true);
		var_dump($endPoints);

		$this->syncService->syncCalendar(
			'remote.php/dav/calendars/admin/fedtest/',
			'principals/users/admin',
			$trustedServer,
		);

		$progress->finish();
		$output->writeln('');

		return 0;
	}
}
