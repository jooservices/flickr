<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;

final class PhotoSizeData extends Dto
{
    public function __construct(
        public string $label,
        public string $source,
        public int $width,
        public int $height,
    ) {}
}
