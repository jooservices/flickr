<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;

final class PhotoMetadataData extends Dto
{
    public function __construct(
        public string $title,
        public ?string $description = null,
    ) {}
}
