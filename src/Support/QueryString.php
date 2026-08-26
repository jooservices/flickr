<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use InvalidArgumentException;

/**
 * RFC 3986 query/form encoding without PHP's native form parser: dotted
 * keys, spaces, `+`, UTF-8 and repeated values survive unchanged.
 */
final class QueryString
{
    /**
     * @param array<string, string|list<string>> $parameters
     */
    public static function build(array $parameters): string
    {
        $pairs = [];

        foreach ($parameters as $key => $value) {
            $encodedKey = rawurlencode($key);
            foreach (is_array($value) ? $value : [$value] as $single) {
                $pairs[] = $encodedKey . '=' . rawurlencode($single);
            }
        }

        return implode('&', $pairs);
    }

    /**
     * Decodes an application/x-www-form-urlencoded body or query string.
     *
     * @return array<string, string|list<string>>
     */
    public static function parseForm(string $raw): array
    {
        $parameters = [];

        foreach (explode('&', $raw) as $pair) {
            if ($pair === '') {
                continue;
            }

            [$keyPart, $valuePart] = array_pad(explode('=', $pair, 2), 2, null);
            $key = self::decodeFormComponent((string) $keyPart);
            if ($key === '') {
                throw new InvalidArgumentException('Encountered a blank parameter name.');
            }

            $value = $valuePart === null ? '' : self::decodeFormComponent($valuePart);
            $parameters[$key][] = $value;
        }

        return array_map(
            static fn(array $values): string|array => count($values) === 1 ? $values[0] : $values,
            $parameters,
        );
    }

    private static function decodeFormComponent(string $component): string
    {
        return rawurldecode(str_replace('+', ' ', $component));
    }
}
