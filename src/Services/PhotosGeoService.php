<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosGeoServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosGeoService extends AbstractRawService implements PhotosGeoServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function batchCorrectLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.batchCorrectLocation', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function correctLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.correctLocation', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.getLocation', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPerms(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.getPerms', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function photosForLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.photosForLocation', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function removeLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.removeLocation', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setContext(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.setContext', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setLocation(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.setLocation', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setPerms(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.geo.setPerms', $parameters);
    }
}
