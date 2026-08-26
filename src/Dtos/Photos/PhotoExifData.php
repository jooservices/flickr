<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

final class PhotoExifData
{
    /**
     * @param list<ExifEntryData> $exif
     */
    public function __construct(
        public readonly string $photoId,
        public readonly array $exif,
    ) {
    }
}
