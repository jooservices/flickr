<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\GalleryServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class GalleryService extends AbstractRawService implements GalleryServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function addPhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.addPhoto', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function create(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.create', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editMeta(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.editMeta', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editPhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.editPhoto', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.editPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListForPhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.getListForPhoto', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.getPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function removePhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.galleries.removePhoto', $parameters);
    }
}
