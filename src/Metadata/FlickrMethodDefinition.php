<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Metadata;

use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;

final class FlickrMethodDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly HttpMethod $httpMethod,
        public readonly AuthPermission $permission,
        public readonly bool $cacheable,
        public readonly bool $available = true,
        public readonly ?string $deprecationReason = null,
        public readonly ?string $docsUrl = null,
    ) {
    }

    public function requiresAuth(): bool
    {
        return $this->permission !== AuthPermission::None;
    }
}
