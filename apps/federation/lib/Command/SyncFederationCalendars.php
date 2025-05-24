<?php

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Federation\Command;

use OCA\DAV\CalDAV\Federation\FederatedCalendarMapper;
use OCA\DAV\CalDAV\Federation\FederatedCalendarSyncService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFederationCalendars extends Command {
	public function __construct(
		private readonly FederatedCalendarSyncService $syncService,
		private readonly FederatedCalendarMapper $federatedCalendarMapper,
	) {
		parent::__construct();
	}

	protected function configure() {
		$this
			->setName('federation:sync-calendars')
			->setDescription('TODO');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$calendarCount = $this->federatedCalendarMapper->countAll();

		$progress = new ProgressBar($output, $calendarCount);
		$progress->start();

		$calendars = $this->federatedCalendarMapper->findAll();
		foreach ($calendars as $calendar) {
			try {
				$this->syncService->syncOne($calendar);
			} catch (\Exception $e) {
				$url = $calendar->getUri();
				$msg = $e->getMessage();
				$output->writeln('<error>Failed to sync calendar $url: $msg</error>');
			}

			$progress->advance();
		}

		$progress->finish();
		$output->writeln('');

		return 0;
	}
}
