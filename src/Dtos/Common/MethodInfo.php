<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Common;

use JOOservices\Flickr\Metadata\FlickrMethodDefinition;

final class MethodInfo
{
    public static function fromDefinition(FlickrMethodDefinition $definition): self
    {
        return new self(
            name: $definition->name,
            httpMethod: $definition->httpMethod,
            permission: $definition->permission,
            cacheable: $definition->cacheable,
            available: $definition->available,
            deprecationReason: $definition->deprecationReason,
            docsUrl: $definition->docsUrl,
        );
    }

    public function __construct(
        public readonly string $name,
        public readonly \JOOservices\Flickr\Enums\HttpMethod $httpMethod,
        public readonly \JOOservices\Flickr\Enums\AuthPermission $permission,
        public readonly bool $cacheable,
        public readonly bool $available,
        public readonly ?string $deprecationReason = null,
        public readonly ?string $docsUrl = null,
    ) {
    }
}
