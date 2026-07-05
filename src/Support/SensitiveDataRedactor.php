<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

/**
 * @internal
 */
final class SensitiveDataRedactor
{
    /**
     * @var list<string>
     */
    private const SENSITIVE_KEYS = [
        'api_key',
        'oauth_token',
        'oauth_signature',
        'oauth_consumer_key',
        'oauth_token_secret',
    ];

    public function redact(string $message): string
    {
        $redacted = preg_replace('/\?.*$/', '?[redacted]', $message) ?? $message;

        foreach (self::SENSITIVE_KEYS as $key) {
            $pattern = '/'.preg_quote($key, '/').'=[^&\s"\']+/i';
            $redacted = preg_replace($pattern, $key.'=[redacted]', $redacted) ?? $redacted;
        }

        return $redacted;
    }
}
