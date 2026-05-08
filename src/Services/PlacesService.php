<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PlacesServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PlacesService extends AbstractRawService implements PlacesServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function find(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.find', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function findByLatLon(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.findByLatLon', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getChildrenWithPhotosPublic(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.getChildrenWithPhotosPublic', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfoByUrl(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.getInfoByUrl', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPlaceTypes(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.getPlaceTypes', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getShapeHistory(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.getShapeHistory', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTopPlacesList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.getTopPlacesList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function placesForBoundingBox(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.placesForBoundingBox', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function placesForContacts(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.placesForContacts', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function placesForTags(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.placesForTags', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function placesForUser(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.placesForUser', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function resolvePlaceId(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.resolvePlaceId', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function resolvePlaceURL(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.resolvePlaceURL', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function tagsForPlace(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.places.tagsForPlace', $parameters);
    }
}
