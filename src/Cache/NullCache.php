<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Cache;

use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;

final class NullCache implements FlickrCacheContract
{
    public function get(string $key): mixed
    {
        return null;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void {}

    public function forget(string $key): void {}
}
