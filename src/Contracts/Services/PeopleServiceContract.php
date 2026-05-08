<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface PeopleServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function findByEmail(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function findByUsername(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getGroups(array $parameters = []): ApiResponseData;

    public function getInfo(string $userId): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getLimits(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotos(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotosOf(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicGroups(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicPhotos(array $parameters = []): ApiResponseData;

    public function getUploadStatus(): ApiResponseData;
}
