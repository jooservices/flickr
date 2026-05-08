<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Upload;

use JOOservices\Dto\Core\Dto;

final class UploadTicketData extends Dto
{
    public function __construct(
        public string $id,
        public ?string $photoId = null,
        public ?bool $complete = null,
        public ?bool $invalid = null,
    ) {}
}
