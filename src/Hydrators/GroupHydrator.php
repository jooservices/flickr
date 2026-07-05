<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Groups\GroupPoolData;

final class GroupHydrator
{
    /**
     * @return list<GroupPoolData>
     */
    public function poolPhotos(ApiResponseData $response): array
    {
        $photos = ResponseItemExtractor::listItems($response, ['photos.photo', 'photo']);

        return array_map(
            static fn (array $photo): GroupPoolData => new GroupPoolData($photo),
            $photos,
        );
    }
}
