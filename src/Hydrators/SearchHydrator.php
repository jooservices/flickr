<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Photos\SearchResultData;

final class SearchHydrator
{
    public static function fromResponse(ApiResponseData $response): SearchResultData
    {
        $container = $response->mapAt('photos');

        return new SearchResultData(
            photos: PhotoHydrator::many($response->listAt('photos', 'photo')),
            page: self::nonNegativeInt($container['page'] ?? null),
            pages: self::nonNegativeInt($container['pages'] ?? null),
            perPage: self::nonNegativeInt($container['perpage'] ?? $container['per_page'] ?? null),
            total: self::nonNegativeInt($container['total'] ?? null),
        );
    }

    private static function nonNegativeInt(mixed $value): int
    {
        if (is_int($value)) {
            return max(0, $value);
        }

        if (is_string($value) && preg_match('/^\d+$/', $value) === 1) {
            return (int) $value;
        }

        return 0;
    }
}
