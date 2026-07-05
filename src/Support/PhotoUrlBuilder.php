<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\DTO\Photos\PhotoData;
use JOOservices\Flickr\DTO\Photos\PhotoInfoData;
use JOOservices\Flickr\Enums\PhotoSize;

final class PhotoUrlBuilder
{
    public function sizeUrl(PhotoData|PhotoInfoData $photo, PhotoSize $size): string
    {
        $id = $photo->id;
        $secret = $photo instanceof PhotoInfoData
            ? (string) ($photo->attributes['secret'] ?? '')
            : '';
        $server = $photo instanceof PhotoInfoData
            ? (string) ($photo->attributes['server'] ?? '')
            : '';

        if ($id === '' || $secret === '' || $server === '') {
            return '';
        }

        $suffix = $size->value;

        return "https://live.staticflickr.com/{$server}/{$id}_{$secret}{$suffix}.jpg";
    }
}
