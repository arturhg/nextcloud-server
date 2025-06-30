<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAV\Paginate;

use Generator;
use OCP\ICache;
use OCP\ICacheFactory;
use OCP\IDBConnection;
use OCP\Security\ISecureRandom;
use Sabre\DAV\Xml\Property\ResourceType;

class PaginateCache {
	public const TTL = 60 * 60;
	private const CACHE_COUNT_SUFFIX = 'count';

	private const RESOURCE_TYPE_PROPERTY = '{DAV:}resourcetype';

	private ICache $cache;

	public function __construct(
		private IDBConnection $database,
		private ISecureRandom $random,
		ICacheFactory $cacheFactory,
	) {
		$this->cache = $cacheFactory->createDistributed('pagination_');
	}

	/**
	 * @param string $uri
	 * @param \Iterator $items
	 * @return array{'token': string, 'count': int}
	 */
	public function store(string $uri, \Iterator $items): array {
		$token = $this->random->generate(32);
		$cacheKey = $this->buildCacheKey($uri, $token);

		$count = 0;
		foreach ($items as $item) {
			$this->serializeResourceType($item);
			// Add small margin to avoid fetching valid count and then expired entries
			$this->cache->set($cacheKey . $count, $item, self::TTL + 60);
			++$count;
		}
		$this->cache->set($cacheKey . self::CACHE_COUNT_SUFFIX, $count, self::TTL);

		return ['token' => $token, 'count' => $count];
	}

	/**
	 * @return Generator<mixed>
	 */
	public function get(string $uri, string $token, int $offset, int $count): Generator {
		$cacheKey = $this->buildCacheKey($uri, $token);
		$nbItems = $this->cache->get($cacheKey . self::CACHE_COUNT_SUFFIX);
		if (!$nbItems || $offset > $nbItems) {
			return [];
		}

		$lastItem = min($nbItems, $offset + $count);
		for ($i = $offset; $i < $lastItem; ++$i) {
			$element = $this->cache->get($cacheKey . $i);
			$this->deserializeResourceType($element);
			yield $element;
		}
	}

	/**
	 * The ResourceType class is lost when json_encode is used.
	 * This function serializes it in place so that this does not happen.
	 *
	 * @param array $item
	 * @return void
	 */
	private function serializeResourceType(array &$item): void {
		foreach ($item as &$responseProperties) {
			if (!is_array($responseProperties)) {
				continue;
			}
			foreach ($responseProperties as &$propertyValue) {
				if ($propertyValue instanceof ResourceType) {
					$propertyValue = serialize($propertyValue);
				}
			}
		}
	}

	/**
	 * The ResourceType class in the cache is serialized. This function will
	 * deserialize it in place.
	 *
	 * @param array $item
	 * @return void
	 */
	private function deserializeResourceType(array &$item): void {
		foreach ($item as &$responseProperties) {
			foreach ($responseProperties as $propertyName => &$propertyValue) {
				if ($propertyName === self::RESOURCE_TYPE_PROPERTY) {
					$propertyValue = unserialize(
						$propertyValue,
						[ 'allowed_classes' => [ResourceType::class]]
					);
				}
			}
		}
	}

	public function exists(string $uri, string $token): bool {
		return $this->cache->get($this->buildCacheKey($uri, $token) . self::CACHE_COUNT_SUFFIX) > 0;
	}

	private function buildCacheKey(string $uri, string $token): string {
		return $token . '_' . crc32($uri) . '_';
	}
}
