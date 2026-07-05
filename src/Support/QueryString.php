<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

/**
 * @internal
 */
final class QueryString
{
    public static function encode(string|int|float|bool $value): string
    {
        return rawurlencode((string) $value);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function build(array $parameters): string
    {
        $pairs = [];

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $pairs[] = self::encode($key).'='.self::encode((string) $item);
                }

                continue;
            }

            $pairs[] = self::encode($key).'='.self::encode((string) $value);
        }

        return implode('&', $pairs);
    }
}
