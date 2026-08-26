<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PlacesApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function find(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.find', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function findByLatLon(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.findByLatLon', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getChildrenWithPhotosPublic(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.getChildrenWithPhotosPublic', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.getInfo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfoByUrl(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.getInfoByUrl', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPlaceTypes(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.getPlaceTypes', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getShapeHistory(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.getShapeHistory', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTopPlacesList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.getTopPlacesList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function placesForBoundingBox(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.placesForBoundingBox', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function placesForContacts(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.placesForContacts', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function placesForTags(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.placesForTags', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function placesForUser(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.placesForUser', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function resolvePlaceId(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.resolvePlaceId', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function resolvePlaceURL(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.resolvePlaceURL', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function tagsForPlace(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.places.tagsForPlace', $parameters, $options);
    }
}
