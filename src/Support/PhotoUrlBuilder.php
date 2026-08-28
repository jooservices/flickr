<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Dtos\Photos\PhotoData;
use JOOservices\Flickr\Enums\PhotoSize;

/**
 * Builds live static Flickr photo URLs. Returns `null` instead of a malformed
 * URL whenever metadata is incomplete.
 */
final class PhotoUrlBuilder
{
    public static function build(PhotoData $photo, PhotoSize|string|null $size = null): ?string
    {
        if ($photo->server === null || $photo->server === '' || $photo->secret === null || $photo->secret === '' || $photo->id === '') {
            return null;
        }

        $size = $size instanceof PhotoSize ? $size->value : $size;
        $suffix = $size === null || $size === '' ? '' : '_' . $size;

        return sprintf('https://live.staticflickr.com/%s/%s_%s%s.jpg', $photo->server, $photo->id, $photo->secret, $suffix);
    }
}
