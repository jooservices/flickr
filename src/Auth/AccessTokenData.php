<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Enums\AuthPermission;

final class AccessTokenData
{
    public function __construct(
        public readonly string $token,
        public readonly string $tokenSecret,
        public readonly AuthPermission $permission = AuthPermission::None,
    ) {
    }
}
