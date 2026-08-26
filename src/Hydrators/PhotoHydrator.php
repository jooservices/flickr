<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\Dtos\Photos\PhotoData;

final class PhotoHydrator
{
    /**
     * Small deterministic mapper; unknown keys are ignored and malformed
     * entries are never coerced into fake photos.
     *
     * @param list<array<string, mixed>> $items
     *
     * @return list<PhotoData>
     */
    public static function many(array $items): array
    {
        $photos = [];

        foreach ($items as $item) {
            if (is_string($item['id'] ?? null) && ($item['id'] !== '')) {
                $photos[] = self::one($item);
            }
        }

        return $photos;
    }

    /**
     * @param array<string, mixed> $item
     */
    public static function one(array $item): PhotoData
    {
        $rawId = $item['id'] ?? null;

        return new PhotoData(
            id: is_int($rawId) || is_float($rawId) || is_string($rawId) ? (string) $rawId : '',
            owner: self::stringOrNull($item, 'owner'),
            title: self::stringOrNull($item, 'title'),
            secret: self::stringOrNull($item, 'secret'),
            server: self::stringOrNull($item, 'server'),
            farm: self::intOrNull($item, 'farm'),
        );
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function stringOrNull(array $item, string $key): ?string
    {
        $value = $item[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function intOrNull(array $item, string $key): ?int
    {
        $value = $item[$key] ?? null;

        return is_numeric($value) === true ? (int) $value : null;
    }
}
