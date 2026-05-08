<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Services\PhotosetServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Photosets\CreatePhotosetData;

final class PhotosetService extends AbstractRawService implements PhotosetServiceContract
{
    public function addPhoto(string $photosetId, string $photoId): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.addPhoto', [
            'photoset_id' => $this->requireId($photosetId, 'photoset'),
            'photo_id' => $this->requireId($photoId, 'photo'),
        ]);
    }

    public function create(CreatePhotosetData $data): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.create', [
            'title' => $data->title,
            'primary_photo_id' => $data->primaryPhotoId,
            'description' => $data->description,
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function delete(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.delete', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editMeta(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.editMeta', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.editPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContext(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.getContext', $parameters);
    }

    public function getInfo(string $photosetId): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.getInfo', ['photoset_id' => $this->requireId($photosetId, 'photoset')]);
    }

    public function getList(?string $userId = null): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.getList', ['user_id' => $userId]);
    }

    public function getPhotos(string $photosetId, array $extras = [], int $page = 1, int $perPage = 100): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.getPhotos', [
            'photoset_id' => $this->requireId($photosetId, 'photoset'),
            'extras' => $extras,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function orderSets(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.orderSets', $parameters);
    }

    public function removePhoto(string $photosetId, string $photoId): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.removePhoto', [
            'photoset_id' => $this->requireId($photosetId, 'photoset'),
            'photo_id' => $this->requireId($photoId, 'photo'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function removePhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.removePhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function reorderPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.reorderPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setPrimaryPhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.setPrimaryPhoto', $parameters);
    }

    private function requireId(string $id, string $name): string
    {
        if (trim($id) === '') {
            throw new InvalidArgumentException("Flickr {$name} id is required.");
        }

        return $id;
    }
}
