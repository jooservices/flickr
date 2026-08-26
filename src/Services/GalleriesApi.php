<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class GalleriesApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function addPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.addPhoto', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function create(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.create', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editMeta(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.editMeta', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.editPhoto', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.editPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.getInfo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getListForPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.getListForPhoto', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.getPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function removePhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.galleries.removePhoto', $parameters, $options);
    }
}
