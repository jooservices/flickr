<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use InvalidArgumentException;

/**
 * @internal
 */
final class ListNormalizer
{
    /**
     * @param  array<array-key, mixed>  $values
     * @return list<string>
     */
    public static function requireNonEmptyTrimmedList(array $values, string $what): array
    {
        $normalized = array_values(array_filter(
            array_map(static fn (mixed $val): string => trim((string) $val), $values),
            static fn (string $val): bool => $val !== ''
        ));

        if ($normalized === []) {
            throw new InvalidArgumentException("At least one {$what} is required.");
        }

        return $normalized;
    }
}
