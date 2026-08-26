<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use InvalidArgumentException;
use JOOservices\Flickr\Enums\HttpMethod;

final class SignatureBaseStringBuilder
{
    /**
     * @param array<string, string|list<string>> $parameters
     */
    public function build(HttpMethod $method, string $baseUri, array $parameters): string
    {
        $pairs = PercentEncoding::encodedPairs($parameters);
        $pairString = implode('&', array_map(
            static fn(array $pair): string => $pair[0] . '=' . $pair[1],
            $pairs,
        ));

        return implode('&', [
            strtoupper($method->value),
            PercentEncoding::encode(self::normalizeBaseUri($baseUri)),
            PercentEncoding::encode($pairString),
        ]);
    }

    public static function normalizeBaseUri(string $uri): string
    {
        $parts = parse_url($uri);
        if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
            throw new InvalidArgumentException('OAuth base URI must contain scheme and host.');
        }

        $base = strtolower($parts['scheme']) . '://' . strtolower($parts['host']);
        $port = $parts['port'] ?? null;

        $defaultPort = strtolower($parts['scheme']) === 'https' ? 443 : 80;
        if ($port !== null && $port !== $defaultPort) {
            $base .= ':' . $port;
        }

        $base .= $parts['path'] ?? '/';

        return $base;
    }
}
