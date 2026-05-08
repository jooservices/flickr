<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;

final class PhotoData extends Dto
{
    public function __construct(
        public string $id,
        public ?string $title = null,
        public ?string $owner = null,
    ) {}
}
