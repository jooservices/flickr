<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Metadata;

use JOOservices\Flickr\Enums\HttpMethod;

/**
 * @internal
 */
final class FlickrMethodRegistry
{
    /**
     * @param  array<string, FlickrMethodDefinition>  $methods
     */
    public function __construct(private array $methods) {}

    public static function default(): self
    {
        /** @var array<string, FlickrMethodDefinition> $methods */
        $methods = require __DIR__.'/methods.php';

        return new self($methods);
    }

    public function find(string $method): FlickrMethodDefinition
    {
        // Unknown methods intentionally receive permissive defaults so raw fallback keeps working.
        return $this->methods[$method] ?? new FlickrMethodDefinition(
            name: $method,
            requiresAuth: false,
            cacheable: false,
            httpMethod: HttpMethod::Get,
        );
    }
}
