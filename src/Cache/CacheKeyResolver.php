<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Cache;

final class CacheKeyResolver
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function resolve(string $method, array $parameters): string
    {
        $parameters = $this->sortParameters($parameters);

        return 'flickr:'.hash('xxh3', $method.serialize($parameters));
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function sortParameters(array $parameters): array
    {
        ksort($parameters);

        foreach ($parameters as $key => $value) {
            $parameters[$key] = $this->sortValue($value);
        }

        return $parameters;
    }

    private function sortValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map($this->sortValue(...), $value);
        }

        return $this->sortParameters($value);
    }
}
