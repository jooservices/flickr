<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Metadata;

use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;

/**
 * @internal
 */
final class FlickrMethodDefinition
{
    public function __construct(
        public string $name,
        public bool $requiresAuth = false,
        public ?AuthPermission $authPermission = null,
        public bool $cacheable = false,
        public HttpMethod $httpMethod = HttpMethod::Get,
        public ?string $docsUrl = null,
        public bool $deprecated = false,
        public ?string $availabilityNote = null,
    ) {}
}
