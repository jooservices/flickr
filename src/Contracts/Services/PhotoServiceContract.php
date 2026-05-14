<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;

interface PhotoServiceContract
{
    /**
     * @param  list<string>  $tags
     */
    public function addTags(string $photoId, array $tags): ApiResponseData;

    public function delete(string $photoId): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAllContexts(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContactsPhotos(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContactsPublicPhotos(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContext(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getCounts(array $parameters = []): ApiResponseData;

    public function getExif(string $photoId): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getFavorites(array $parameters = []): ApiResponseData;

    public function getInfo(string $photoId): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getNotInSet(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPerms(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPopular(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getRecent(array $parameters = []): ApiResponseData;

    public function getSizes(string $photoId): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getUntagged(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getWithGeoData(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getWithoutGeoData(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function recentlyUpdated(array $parameters = []): ApiResponseData;

    public function removeTag(string $tagId): ApiResponseData;

    public function search(SearchPhotosData $data): ApiResponseData;

    /**
     * @return iterable<ApiResponseData>
     */
    public function searchPages(
        SearchPhotosData $data,
        ?PaginationOptionsData $pagination = null,
        ?RequestOptionsData $requestOptions = null,
    ): iterable;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setContentType(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setDates(array $parameters = []): ApiResponseData;

    public function setMeta(string $photoId, string $title, ?string $description = null): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setPerms(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setSafetyLevel(array $parameters = []): ApiResponseData;

    /**
     * @param  list<string>  $tags
     */
    public function setTags(string $photoId, array $tags): ApiResponseData;
}
