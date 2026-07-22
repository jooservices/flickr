<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Metadata;

use JOOservices\Flickr\DTO\Metadata\MethodInfo;
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

    public function has(string $method): bool
    {
        return isset($this->methods[$method]);
    }

    public function describe(string $method): ?MethodInfo
    {
        $definition = $this->methods[$method] ?? null;
        if ($definition === null) {
            return null;
        }

        return new MethodInfo(
            name: $definition->name,
            requiresAuth: $definition->requiresAuth,
            authPermission: $definition->authPermission,
            cacheable: $definition->cacheable,
            httpMethod: $definition->httpMethod,
            docsUrl: $definition->docsUrl,
            deprecated: $definition->deprecated,
            availabilityNote: $definition->availabilityNote,
        );
    }

    public function suggestionFor(string $method): ?string
    {
        if (isset($this->methods[$method])) {
            return null;
        }

        $closest = null;
        $bestDistance = PHP_INT_MAX;

        foreach (array_keys($this->methods) as $known) {
            $distance = levenshtein($method, $known);
            if ($distance < $bestDistance && $distance <= 3) {
                $bestDistance = $distance;
                $closest = $known;
            }
        }

        return $closest;
    }
}
