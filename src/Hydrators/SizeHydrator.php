<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\Dtos\Photos\PhotoSizeData;

final class SizeHydrator
{
    /**
     * @param list<array<string, mixed>> $items
     *
     * @return list<PhotoSizeData>
     */
    public static function many(array $items): array
    {
        $sizes = [];

        foreach ($items as $item) {
            $label = $item['label'] ?? null;
            $source = $item['source'] ?? null;

            if (is_string($label) === false || is_string($source) === false) {
                continue;
            }

            $sizes[] = new PhotoSizeData(
                label: $label,
                source: $source,
                width: self::intOrNull($item, 'width'),
                height: self::intOrNull($item, 'height'),
            );
        }

        return $sizes;
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
