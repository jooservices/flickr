<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;

final class ArrayCache implements FlickrCacheContract
{
    /**
     * @var array<string, mixed>
     */
    public array $items = [];

    public int $gets = 0;

    public int $puts = 0;

    public ?int $lastTtl = null;

    public function get(string $key): mixed
    {
        $this->gets++;

        return $this->items[$key] ?? null;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->puts++;
        $this->lastTtl = $ttl;
        $this->items[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($this->items[$key]);
    }
}
