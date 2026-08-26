<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

use JOOservices\Flickr\Dtos\Common\ApiResponseData;

interface FlickrCache
{
    public function get(string $key): ?ApiResponseData;

    public function put(string $key, ApiResponseData $value, int $ttl): void;
}
