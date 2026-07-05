<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Photosets\PhotosetData;
use JOOservices\Flickr\DTO\Photosets\PhotosetPhotoData;

final class PhotosetHydrator
{
    /**
     * @return list<PhotosetData>
     */
    public function list(ApiResponseData $response): array
    {
        $sets = ResponseItemExtractor::listItems($response, ['photosets.photoset', 'photoset']);

        return array_map(
            static fn (array $set): PhotosetData => new PhotosetData(
                id: (string) ($set['id'] ?? ''),
                title: isset($set['title']) ? (string) $set['title'] : null,
            ),
            $sets,
        );
    }

    /**
     * @return list<PhotosetPhotoData>
     */
    public function photos(ApiResponseData $response): array
    {
        $photos = ResponseItemExtractor::listItems($response, ['photoset.photo', 'photos.photo', 'photo']);

        return array_map(
            static fn (array $photo): PhotosetPhotoData => new PhotosetPhotoData(
                id: (string) ($photo['id'] ?? ''),
                title: isset($photo['title']) ? (string) $photo['title'] : null,
            ),
            $photos,
        );
    }
}
