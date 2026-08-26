<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\Api;
use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

abstract class AbstractApiService
{
    public function __construct(protected readonly Api $api)
    {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    final protected function call(string $method, array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->api->call($method, $parameters, $options);
    }
}
