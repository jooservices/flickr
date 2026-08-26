<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class UrlsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getGroup(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.urls.getGroup', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getUserPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.urls.getUserPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getUserProfile(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.urls.getUserProfile', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function lookupGallery(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.urls.lookupGallery', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function lookupGroup(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.urls.lookupGroup', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function lookupUser(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.urls.lookupUser', $parameters, $options);
    }
}
