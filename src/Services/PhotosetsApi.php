<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosetsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function addPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.addPhoto', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function create(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.create', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function delete(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.delete', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editMeta(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.editMeta', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.editPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getContext(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.getContext', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.getInfo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.getPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function orderSets(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.orderSets', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function removePhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.removePhoto', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function removePhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.removePhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function reorderPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.reorderPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setPrimaryPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.setPrimaryPhoto', $parameters, $options);
    }
}
