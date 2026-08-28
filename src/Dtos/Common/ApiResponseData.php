<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Common;

final class ApiResponseData
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(
        public readonly bool $ok,
        public readonly array $data = [],
        public readonly ?ApiErrorData $error = null,
    ) {
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listAt(string ...$path): array
    {
        $current = $this->data;
        foreach ($path as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return [];
            }
            $current = $current[$segment];
        }

        if (is_array($current) === false) {
            return [];
        }

        if (array_is_list($current) === false) {
            $current = [$current];
        }

        /** @var list<array<string, mixed>> */
        return array_values(array_filter(
            $current,
            static fn(mixed $item): bool => is_array($item),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function mapAt(string ...$path): array
    {
        $current = $this->data;
        foreach ($path as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return [];
            }
            $current = $current[$segment];
        }

        /** @var array<string, mixed> */
        return is_array($current) ? $current : [];
    }
}
