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
        ksort($parameters);

        return 'flickr:'.hash('xxh3', $method.serialize($parameters));
    }
}
