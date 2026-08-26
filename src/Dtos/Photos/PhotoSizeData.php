<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

final class PhotoSizeData
{
    public function __construct(
        public readonly string $label,
        public readonly string $source,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
    ) {
    }
}
