<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Favorites\FavoritePhotoData;

final class FavoriteHydrator
{
    /**
     * @return list<FavoritePhotoData>
     */
    public function list(ApiResponseData $response): array
    {
        $photos = ResponseItemExtractor::listItems($response, ['photos.photo', 'photo']);

        return array_map(
            static fn (array $photo): FavoritePhotoData => new FavoritePhotoData(
                id: (string) ($photo['id'] ?? ''),
                title: isset($photo['title']) ? (string) $photo['title'] : null,
            ),
            $photos,
        );
    }
}
