<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Client\FlickrErrorCodeMap;
use JOOservices\Flickr\Dtos\Common\ApiErrorData;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Photos\PhotoExifData;
use JOOservices\Flickr\Dtos\Photos\PhotoInfoData;
use JOOservices\Flickr\Dtos\Photos\SearchPhotosData;
use JOOservices\Flickr\Dtos\Photos\SearchResultData;
use JOOservices\Flickr\Dtos\Photos\PhotoSizeData;
use JOOservices\Flickr\Hydrators\InfoHydrator;
use JOOservices\Flickr\Hydrators\SearchHydrator;
use JOOservices\Flickr\Hydrators\SizeHydrator;

/**
 * Hand-written service: the five priority photo workflows return typed
 * results; every remaining registry method keeps its generated generic
 * wrapper. Do not regenerate this file from the manifest.
 */
final class PhotosApi extends AbstractApiService
{
    public function search(SearchPhotosData $query, ?ApiCallOptions $options = null): SearchResultData
    {
        return SearchHydrator::fromResponse($this->typedCall('flickr.photos.search', $query->toArray(), $options));
    }

    public function getRecent(?SearchPhotosData $query = null, ?ApiCallOptions $options = null): SearchResultData
    {
        return SearchHydrator::fromResponse(
            $this->typedCall('flickr.photos.getRecent', ($query ?? new SearchPhotosData())->toArray(), $options),
        );
    }

    public function getInfo(string $photoId): PhotoInfoData
    {
        return InfoHydrator::fromResponse($this->typedCall('flickr.photos.getInfo', ['photo_id' => $photoId]));
    }

    /**
     * @return list<PhotoSizeData>
     */
    public function getSizes(string $photoId): array
    {
        return SizeHydrator::many(
            $this->typedCall('flickr.photos.getSizes', ['photo_id' => $photoId])->listAt('sizes', 'size'),
        );
    }

    public function getExif(string $photoId): PhotoExifData
    {
        return InfoHydrator::exifFromResponse($this->typedCall('flickr.photos.getExif', ['photo_id' => $photoId]));
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function typedCall(string $method, array $parameters, ?ApiCallOptions $options = null): ApiResponseData
    {
        $response = $this->call($method, $parameters, self::typedOptions($options));

        if ($response->ok === false) {
            throw FlickrErrorCodeMap::throwFor(
                $response->error ?? new ApiErrorData('unknown', 'Flickr API request failed.'),
            );
        }

        return $response;
    }

    private static function typedOptions(?ApiCallOptions $options): ApiCallOptions
    {
        $options ??= new ApiCallOptions();

        return new ApiCallOptions(
            mode: $options->mode,
            bypassCache: $options->bypassCache,
            throwOnApiError: true,
        );
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function addTags(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.addTags', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function delete(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.delete', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getAllContexts(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getAllContexts', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getContactsPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getContactsPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getContactsPublicPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getContactsPublicPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getContext(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getContext', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getCounts(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getCounts', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getFavorites(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getFavorites', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getNotInSet(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getNotInSet', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPerms(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getPerms', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPopular(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getPopular', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getUntagged(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getUntagged', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getWithGeoData(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getWithGeoData', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getWithoutGeoData(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.getWithoutGeoData', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function recentlyUpdated(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.recentlyUpdated', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function removeTag(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.removeTag', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setContentType(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.setContentType', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setDates(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.setDates', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setMeta(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.setMeta', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setPerms(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.setPerms', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setSafetyLevel(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.setSafetyLevel', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setTags(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.setTags', $parameters, $options);
    }
}
