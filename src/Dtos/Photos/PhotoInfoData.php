<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

final class PhotoInfoData
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $ownerId = null,
        public readonly ?string $ownerUsername = null,
        public readonly ?string $datePosted = null,
        public readonly ?string $dateTaken = null,
        public readonly ?int $views = null,
    ) {
    }
}
