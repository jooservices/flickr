<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use InvalidArgumentException;
use Stringable;

final class ParameterNormalizer
{
    /**
     * Normalizes user input into Flickr wire values. `null` entries are
     * dropped; booleans become `1`/`0`; lists under one key become repeated
     * values. Nested associative arrays are rejected.
     *
     * @param array<string, mixed> $parameters
     *
     * @return array<string, string|list<string>>
     */
    public static function normalize(array $parameters): array
    {
        $normalized = [];

        foreach ($parameters as $key => $value) {
            self::assertKeyName($key);

            foreach (self::flattenValue($value) as $scalar) {
                $normalized[$key][] = $scalar;
            }
        }

        return array_map(
            static fn(array $values): string|array => count($values) === 1 ? $values[0] : $values,
            $normalized,
        );
    }

    private static function assertKeyName(string $key): void
    {
        if ($key === '' || preg_match('/[\r\n\x00-\x1F\x7F]/', $key) === 1) {
            throw new InvalidArgumentException('Parameter names must be non-empty and free of control characters.');
        }
    }

    /**
     * @return list<string>
     */
    private static function flattenValue(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        if (is_array($value)) {
            if (array_is_list($value) === false) {
                throw new InvalidArgumentException('Nested associative arrays are not supported Flickr parameters.');
            }

            $flattened = [];

            foreach ($value as $item) {
                foreach (self::flattenValue($item) as $scalar) {
                    $flattened[] = $scalar;
                }
            }

            return $flattened;
        }

        return [self::stringify($value)];
    }

    private static function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value) || is_string($value)) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw new InvalidArgumentException(sprintf('Parameter of type %s is not representable on the wire.', get_debug_type($value)));
    }
}
