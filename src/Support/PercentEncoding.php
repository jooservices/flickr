<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

final class PercentEncoding
{
    public static function encode(string $value): string
    {
        return rawurlencode($value);
    }

    /**
     * Bytewise sort of percent-encoded pairs by key then value.
     *
     * @param array<string, string|list<string>> $parameters already-normalized values
     *
     * @return list<array{0: string, 1: string}>
     */
    public static function encodedPairs(array $parameters): array
    {
        $pairs = [];

        foreach ($parameters as $key => $value) {
            $encodedKey = self::encode($key);
            foreach (is_array($value) ? $value : [$value] as $single) {
                $pairs[] = [$encodedKey, self::encode($single)];
            }
        }

        usort($pairs, static fn(array $left, array $right): int => [$left[0], $left[1]] <=> [$right[0], $right[1]]);

        return $pairs;
    }
}
