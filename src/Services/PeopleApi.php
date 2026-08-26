<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PeopleApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function findByEmail(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.findByEmail', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function findByUsername(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.findByUsername', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getGroups(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getGroups', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getInfo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getLimits(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getLimits', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotosOf(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getPhotosOf', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPublicGroups(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getPublicGroups', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPublicPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getPublicPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getUploadStatus(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.people.getUploadStatus', $parameters, $options);
    }
}
