<?php

declare(strict_types=1);

use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;

require __DIR__.'/../vendor/autoload.php';

final class ArrayFlickrCache implements FlickrCacheContract
{
    /**
     * @var array<string, mixed>
     */
    private array $items = [];

    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->items[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($this->items[$key]);
    }
}

$cache = new ArrayFlickrCache;
$cache->put('flickr-example', ['stat' => 'ok'], 60);

echo json_encode($cache->get('flickr-example'), JSON_PRETTY_PRINT).PHP_EOL;
