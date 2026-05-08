<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use BackedEnum;

final class ParameterNormalizer
{
    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    public function normalize(array $parameters): array
    {
        $normalized = [];

        foreach ($parameters as $key => $value) {
            if ($value === null) {
                continue;
            }

            $normalized[$key] = $this->normalizeValue($value);
        }

        return $normalized;
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_array($value)) {
            return implode(',', array_map(
                static fn (mixed $item): string => (string) ($item instanceof BackedEnum ? $item->value : $item),
                array_values($value),
            ));
        }

        return $value;
    }
}
