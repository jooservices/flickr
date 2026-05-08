<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photosets;

use JOOservices\Dto\Core\Dto;

final class PhotosetPhotoData extends Dto
{
    public function __construct(
        public string $id,
        public ?string $title = null,
    ) {}
}
