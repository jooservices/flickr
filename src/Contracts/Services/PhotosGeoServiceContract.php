<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface PhotosGeoServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function batchCorrectLocation(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function correctLocation(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getLocation(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPerms(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function photosForLocation(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function removeLocation(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setContext(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setLocation(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setPerms(array $parameters = []): ApiResponseData;
}
