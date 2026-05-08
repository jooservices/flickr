<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\UrlsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class UrlsService extends AbstractRawService implements UrlsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getGroup(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.urls.getGroup', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getUserPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.urls.getUserPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getUserProfile(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.urls.getUserProfile', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function lookupGallery(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.urls.lookupGallery', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function lookupGroup(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.urls.lookupGroup', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function lookupUser(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.urls.lookupUser', $parameters);
    }
}
