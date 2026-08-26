<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

final class ExifEntryData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $raw = null,
        public readonly ?string $clean = null,
    ) {
    }
}
