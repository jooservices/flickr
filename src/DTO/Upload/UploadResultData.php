<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Upload;

use JOOservices\Dto\Core\Dto;

final class UploadResultData extends Dto
{
    public function __construct(
        public ?string $photoId = null,
        public ?string $ticketId = null,
        public ?string $secret = null,
        public ?string $originalSecret = null,
    ) {}
}
