<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Enums\AuthPermission;

final class PendingAuthorization
{
    public const LIFETIME_SECONDS = 600;

    public function __construct(
        public readonly string $requestToken,
        public readonly string $requestTokenSecret,
        public readonly AuthPermission $permission,
        public readonly int $issuedAt,
        public readonly string $authorizationUrl,
    ) {
    }
}
