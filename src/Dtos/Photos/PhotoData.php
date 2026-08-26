<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

final class PhotoData
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $owner = null,
        public readonly ?string $title = null,
        public readonly ?string $secret = null,
        public readonly ?string $server = null,
        public readonly ?int $farm = null,
    ) {
    }
}
