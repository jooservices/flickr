<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosPeopleApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function add(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.people.add', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function delete(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.people.delete', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function deleteCoords(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.people.deleteCoords', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editCoords(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.people.editCoords', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.people.getList', $parameters, $options);
    }
}
