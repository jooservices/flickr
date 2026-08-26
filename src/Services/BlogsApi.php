<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class BlogsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.blogs.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getServices(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.blogs.getServices', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function postPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.blogs.postPhoto', $parameters, $options);
    }
}
