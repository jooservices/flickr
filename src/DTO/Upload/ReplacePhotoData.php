<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Upload;

use InvalidArgumentException;
use JOOservices\Dto\Core\Dto;

final class ReplacePhotoData extends Dto
{
    public function __construct(
        public string $path,
        public string $photoId,
        public bool $async = false,
    ) {
        if (trim($this->photoId) === '') {
            throw new InvalidArgumentException('Photo id is required for replace.');
        }
    }
}
