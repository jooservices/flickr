<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class TagsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getClusterPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getClusterPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getClusters(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getClusters', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getHotList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getHotList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getListPhoto(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getListPhoto', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getListUser(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getListUser', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getListUserPopular(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getListUserPopular', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getListUserRaw(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getListUserRaw', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getMostFrequentlyUsed(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getMostFrequentlyUsed', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getRelated(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.tags.getRelated', $parameters, $options);
    }
}
