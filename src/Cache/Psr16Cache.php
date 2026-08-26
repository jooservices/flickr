<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Cache;

use JOOservices\Flickr\Contracts\FlickrCache;
use JOOservices\Flickr\Exceptions\CacheBackendException;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use Psr\SimpleCache\CacheInterface;
use Throwable;

/**
 * PSR-16 adapter. Backend failures fail clearly with a typed exception — the
 * SDK never fabricates a cached response and never silently swallows backend
 * errors.
 */
final class Psr16Cache implements FlickrCache
{
    public function __construct(private readonly CacheInterface $cache)
    {
    }

    public function get(string $key): ?ApiResponseData
    {
        try {
            $hit = $this->cache->get($key);
        } catch (Throwable $error) {
            throw new CacheBackendException('Cache read failed.', previous: $error);
        }

        return $hit instanceof ApiResponseData ? $hit : null;
    }

    public function put(string $key, ApiResponseData $value, int $ttl): void
    {
        if ($ttl <= 0) {
            return;
        }

        try {
            $this->cache->set($key, $value, $ttl);
        } catch (Throwable $error) {
            throw new CacheBackendException('Cache write failed.', previous: $error);
        }
    }
}
