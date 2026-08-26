<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Cache;

final class CacheKeyResolver
{
    private const VERSION_PREFIX = 'jooflickr.v4.';

    /**
     * Deterministic, delimiter-safe key over the method plus recursively
     * key-sorted normalized parameters.
     *
     * @param array<string, string|list<string>> $parameters
     */
    public static function key(string $method, array $parameters): string
    {
        return self::VERSION_PREFIX . hash('sha256', $method . "\x1F" . (string) json_encode(
            self::sortRecursive($parameters),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ));
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    private static function sortRecursive(array $parameters): array
    {
        foreach ($parameters as $key => $value) {
            $parameters[$key] = is_array($value) ? self::sortRecursive($value) : $value;
        }

        ksort($parameters, SORT_STRING);

        return $parameters;
    }
}
