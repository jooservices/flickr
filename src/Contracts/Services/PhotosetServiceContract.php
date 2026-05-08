<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Photosets\CreatePhotosetData;

interface PhotosetServiceContract
{
    public function addPhoto(string $photosetId, string $photoId): ApiResponseData;

    public function create(CreatePhotosetData $data): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function delete(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editMeta(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editPhotos(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContext(array $parameters = []): ApiResponseData;

    public function getInfo(string $photosetId): ApiResponseData;

    public function getList(?string $userId = null): ApiResponseData;

    /**
     * @param  list<string>  $extras
     */
    public function getPhotos(string $photosetId, array $extras = [], int $page = 1, int $perPage = 100): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function orderSets(array $parameters = []): ApiResponseData;

    public function removePhoto(string $photosetId, string $photoId): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function removePhotos(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function reorderPhotos(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setPrimaryPhoto(array $parameters = []): ApiResponseData;
}
