<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

/**
 * @internal
 */
final class ResponseItemExtractor
{
    /**
     * @param  list<string>  $paths  Dot-separated paths such as photos.photo
     * @return list<array<string, mixed>>
     */
    public static function listItems(ApiResponseData $response, array $paths): array
    {
        foreach ($paths as $path) {
            $value = self::dig($response->data, explode('.', $path));

            if (! is_array($value) || $value === []) {
                continue;
            }

            if (self::isList($value)) {
                /** @var list<array<string, mixed>> $value */
                return $value;
            }

            /** @var array<string, mixed> $value */
            return [$value];
        }

        return [];
    }

    /**
     * @param  list<string>  $paths
     * @return array<string, mixed>|null
     */
    public static function singleItem(ApiResponseData $response, array $paths): ?array
    {
        foreach ($paths as $path) {
            $value = self::dig($response->data, explode('.', $path));

            if (is_array($value) && $value !== []) {
                /** @var array<string, mixed> $value */
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $keys
     */
    private static function dig(array $data, array $keys): mixed
    {
        $current = $data;

        foreach ($keys as $key) {
            if (! is_array($current) || ! array_key_exists($key, $current)) {
                return null;
            }

            $current = $current[$key];
        }

        return $current;
    }

    /**
     * @param  array<mixed>  $value
     */
    private static function isList(array $value): bool
    {
        if ($value === []) {
            return true;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }
}
