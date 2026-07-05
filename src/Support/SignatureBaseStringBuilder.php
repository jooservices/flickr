<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

/**
 * @internal
 */
final class SignatureBaseStringBuilder
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function build(string $method, string $url, array $parameters): string
    {
        $normalized = $this->normalizeParameters($parameters);

        return implode('&', [
            QueryString::encode(strtoupper($method)),
            QueryString::encode($this->normalizeUrl($url)),
            QueryString::encode(QueryString::build($normalized)),
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function normalizeParameters(array $parameters): array
    {
        $pairs = [];

        foreach ($parameters as $key => $value) {
            if ($key === 'oauth_signature' || $value === null) {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    $pairs[] = [$key, (string) $item];
                }

                continue;
            }

            $pairs[] = [$key, (string) $value];
        }

        usort($pairs, static function (array $left, array $right): int {
            return [QueryString::encode($left[0]), QueryString::encode($left[1])]
                <=> [QueryString::encode($right[0]), QueryString::encode($right[1])];
        });

        $normalized = [];

        foreach ($pairs as [$key, $value]) {
            if (array_key_exists($key, $normalized)) {
                $normalized[$key] = (array) $normalized[$key];
                $normalized[$key][] = $value;

                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    private function normalizeUrl(string $url): string
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $port = $parts['port'] ?? null;

        $authority = $host;
        if (is_int($port) && ! (($scheme === 'https' && $port === 443) || ($scheme === 'http' && $port === 80))) {
            $authority .= ':'.$port;
        }

        return $scheme.'://'.$authority.$path;
    }
}
