<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photosets;

use InvalidArgumentException;
use JOOservices\Dto\Core\Dto;

final class CreatePhotosetData extends Dto
{
    public function __construct(
        public string $title,
        public string $primaryPhotoId,
        public ?string $description = null,
    ) {
        if (trim($this->title) === '' || trim($this->primaryPhotoId) === '') {
            throw new InvalidArgumentException('Photoset title and primary photo id are required.');
        }
    }
}
