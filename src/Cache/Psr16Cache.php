<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Cache;

use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;
use Psr\SimpleCache\CacheInterface;

final class Psr16Cache implements FlickrCacheContract
{
    public function __construct(private CacheInterface $cache) {}

    public function get(string $key): mixed
    {
        return $this->cache->get($key);
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->cache->set($key, $value, $ttl);
    }

    public function forget(string $key): void
    {
        $this->cache->delete($key);
    }
}
