<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;

final class PhotoPermissionData extends Dto
{
    public function __construct(
        public bool $isPublic,
        public bool $isFriend,
        public bool $isFamily,
    ) {}
}
