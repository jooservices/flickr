<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Photos\ExifEntryData;
use JOOservices\Flickr\Dtos\Photos\PhotoExifData;
use JOOservices\Flickr\Dtos\Photos\PhotoInfoData;

final class InfoHydrator
{
    public static function fromResponse(ApiResponseData $response): PhotoInfoData
    {
        $photo = $response->mapAt('photo');

        return new PhotoInfoData(
            id: self::str($photo['id'] ?? null) ?? '',
            title: self::content($photo['title'] ?? null),
            description: self::content($photo['description'] ?? null),
            ownerId: self::ownerField($photo, 'nsid'),
            ownerUsername: self::ownerField($photo, 'username'),
            datePosted: self::firstString($photo, 'dateposted', 'date_posted'),
            dateTaken: self::str($photo['datetaken'] ?? null),
            views: self::intish($photo['views'] ?? null),
        );
    }

    public static function exifFromResponse(ApiResponseData $response): PhotoExifData
    {
        $photo = $response->mapAt('photo');
        $entries = [];

        foreach ($response->listAt('photo', 'exif') as $entry) {
            $name = $entry['label'] ?? null;

            if (is_string($name) === false) {
                continue;
            }

            $raw = is_array($entry['raw'] ?? null) ? ($entry['raw']['_content'] ?? null) : null;
            $clean = is_array($entry['clean'] ?? null) ? ($entry['clean']['_content'] ?? null) : null;

            $entries[] = new ExifEntryData(
                name: $name,
                raw: is_string($raw) ? $raw : null,
                clean: is_string($clean) ? $clean : null,
            );
        }

        return new PhotoExifData(self::str($photo['id'] ?? null) ?? '', $entries);
    }

    private static function content(mixed $value): ?string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value) === false) {
            return null;
        }

        $inner = $value['_content'] ?? null;

        return is_string($inner) ? $inner : null;
    }

    /**
     * @param array<string, mixed> $photo
     */
    private static function ownerField(array $photo, string $key): ?string
    {
        $owner = $photo['owner'] ?? null;

        if (is_array($owner) === false) {
            return null;
        }

        $value = $owner[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * @param array<string, mixed> $photo
     */
    private static function firstString(array $photo, string ...$keys): ?string
    {
        foreach ($keys as $key) {
            $value = self::str($photo[$key] ?? null);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private static function str(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    private static function intish(mixed $value): ?int
    {
        return is_numeric($value) === true ? (int) $value : null;
    }
}
