<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Places\PlaceData;

final class PlaceHydrator
{
    public function place(ApiResponseData $response): PlaceData
    {
        $place = ResponseItemExtractor::singleItem($response, ['place']);

        if ($place === null) {
            return new PlaceData('');
        }

        $placeId = (string) ($place['place_id'] ?? $place['woeid'] ?? $place['id'] ?? '');
        $name = isset($place['name']) ? (string) $place['name'] : null;
        unset($place['place_id'], $place['woeid'], $place['id'], $place['name']);

        return new PlaceData($placeId, $name, $place);
    }

    /**
     * @return list<PlaceData>
     */
    public function places(ApiResponseData $response): array
    {
        $places = ResponseItemExtractor::listItems($response, ['places.place', 'place']);

        return array_map(function (array $place): PlaceData {
            $placeId = (string) ($place['place_id'] ?? $place['woeid'] ?? $place['id'] ?? '');
            $name = isset($place['name']) ? (string) $place['name'] : null;
            unset($place['place_id'], $place['woeid'], $place['id'], $place['name']);

            return new PlaceData($placeId, $name, $place);
        }, $places);
    }
}
