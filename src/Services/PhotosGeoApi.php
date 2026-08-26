<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosGeoApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function batchCorrectLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.batchCorrectLocation', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function correctLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.correctLocation', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.getLocation', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPerms(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.getPerms', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function photosForLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.photosForLocation', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function removeLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.removeLocation', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setContext(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.setContext', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setLocation(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.setLocation', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setPerms(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.geo.setPerms', $parameters, $options);
    }
}
