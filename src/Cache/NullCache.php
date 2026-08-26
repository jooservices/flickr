<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Cache;

use JOOservices\Flickr\Contracts\FlickrCache;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

final class NullCache implements FlickrCache
{
    public function get(string $key): ?ApiResponseData
    {
        return null;
    }

    public function put(string $key, ApiResponseData $value, int $ttl): void
    {
    }
}
