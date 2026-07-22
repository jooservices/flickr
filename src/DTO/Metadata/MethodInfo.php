<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Metadata;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Flickr;

/**
 * Public method metadata for introspection via {@see Flickr::describe()}.
 */
final class MethodInfo extends Dto
{
    public function __construct(
        public readonly string $name,
        public readonly bool $requiresAuth = false,
        public readonly ?AuthPermission $authPermission = null,
        public readonly bool $cacheable = false,
        public readonly HttpMethod $httpMethod = HttpMethod::Get,
        public readonly ?string $docsUrl = null,
        public readonly bool $deprecated = false,
        public readonly ?string $availabilityNote = null,
    ) {}
}
