<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class GroupsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.getInfo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function join(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.join', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function joinRequest(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.joinRequest', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function leave(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.leave', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function search(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.groups.search', $parameters, $options);
    }
}
