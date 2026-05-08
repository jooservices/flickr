<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Auth;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Enums\AuthPermission;

final class AuthorizationUrlData extends Dto
{
    public function __construct(
        public RequestTokenData $requestToken,
        public AuthPermission $permission,
    ) {}
}
