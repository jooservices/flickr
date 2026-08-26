<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class TestApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function echo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.test.echo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function login(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.test.login', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function null(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.test.null', $parameters, $options);
    }
}
