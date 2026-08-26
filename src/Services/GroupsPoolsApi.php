<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class GroupsPoolsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function add(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.pools.add', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getContext(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.pools.getContext', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getGroups(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.pools.getGroups', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.pools.getPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function remove(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.pools.remove', $parameters, $options);
    }
}
