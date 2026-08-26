<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class FavoritesApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function add(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.favorites.add', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getContext(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.favorites.getContext', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.favorites.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPublicList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.favorites.getPublicList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function remove(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.favorites.remove', $parameters, $options);
    }
}
