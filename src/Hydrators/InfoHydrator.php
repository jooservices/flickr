<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\Client\FlickrErrorCodeMap;
use JOOservices\Flickr\Dtos\Common\ApiErrorData;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Photos\ExifEntryData;
use JOOservices\Flickr\Dtos\Photos\PhotoExifData;
use JOOservices\Flickr\Dtos\Photos\PhotoInfoData;

final class InfoHydrator
{
    public static function fromResponse(ApiResponseData $response): PhotoInfoData
    {
        self::assertOk($response);

        $photo = $response->mapAt('photo');

        return new PhotoInfoData(
            id: self::str($photo['id'] ?? null) ?? '',
            title: self::content($photo['title'] ?? null),
            description: self::content($photo['description'] ?? null),
            ownerId: self::ownerField($photo, 'nsid'),
            ownerUsername: self::ownerField($photo, 'username'),
            datePosted: self::dateValue($photo, 'posted', 'dateposted', 'date_posted'),
            dateTaken: self::dateValue($photo, 'taken', 'datetaken'),
            views: self::intish($photo['views'] ?? null),
        );
    }

    public static function exifFromResponse(ApiResponseData $response): PhotoExifData
    {
        self::assertOk($response);

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
    private static function dateValue(array $photo, string $datesKey, string ...$fallbacks): ?string
    {
        $dates = $photo['dates'] ?? null;

        if (is_array($dates)) {
            $nested = self::stringish($dates[$datesKey] ?? null);

            if ($nested !== null) {
                return $nested;
            }
        }

        foreach ($fallbacks as $key) {
            $value = self::stringish($photo[$key] ?? null);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private static function stringish(mixed $value): ?string
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return is_int($value) ? (string) $value : null;
    }

    private static function assertOk(ApiResponseData $response): void
    {
        if ($response->ok === false) {
            throw FlickrErrorCodeMap::throwFor(
                $response->error ?? new ApiErrorData('unknown', 'Flickr API request failed.'),
            );
        }
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
