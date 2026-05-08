<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Auth;

use JOOservices\Dto\Core\Dto;

final class AuthorizedUserData extends Dto
{
    public function __construct(
        public string $nsid,
        public ?string $username = null,
        public ?string $fullname = null,
    ) {}
}
