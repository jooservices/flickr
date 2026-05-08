<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Cache;

interface FlickrCacheContract
{
    public function get(string $key): mixed;

    public function put(string $key, mixed $value, ?int $ttl = null): void;

    public function forget(string $key): void;
}
