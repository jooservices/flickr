<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

final class SensitiveDataRedactor
{
    private const SECRET_KEY_PATTERNS = [
        'oauth_token_secret',
        'oauth_token',
        'oauth_verifier',
        'oauth_signature',
        'oauth_signature_base_string',
        'oauth_consumer_secret',
        'api_sig',
        'api_key',
        'consumer_secret',
        'authorization',
        'password',
        'secret',
    ];

    /**
     * A fresh `oauth_nonce`/`oauth_signature` (raw and percent-encoded) is
     * registered on every single API call, and nothing ever un-registers
     * one. Without a cap, an instance kept alive across many requests in a
     * long-running process (Swoole, RoadRunner, a persistent worker) grows
     * this set forever, with both memory and per-call redaction cost
     * climbing without bound. 500 entries comfortably covers many recent
     * in-flight requests while keeping both bounded.
     */
    private const MAX_TRACKED_SECRETS = 500;

    /**
     * Insertion-ordered map of a lookup key to the original secret value.
     * A numeric-string secret (e.g. an `oauth_timestamp` value) used
     * directly as an array key would be silently cast to an int by PHP,
     * corrupting `redactText()`'s `str_replace()` call — so every key here
     * is prefixed with a NUL byte, which can never form a valid decimal
     * integer string.
     *
     * @var array<string, string>
     */
    private array $secretValues = [];

    /** @param list<string> $credentials configuration secrets retained for this client's lifetime */
    public function __construct(private readonly array $credentials = [])
    {
    }

    public function registerSecret(string $value): void
    {
        if ($value === '') {
            return;
        }

        $key = "\0" . $value;

        unset($this->secretValues[$key]);
        $this->secretValues[$key] = $value;

        if (count($this->secretValues) > self::MAX_TRACKED_SECRETS) {
            unset($this->secretValues[array_key_first($this->secretValues)]);
        }
    }

    public function redactText(string $text): string
    {
        $replacements = [];
        foreach ([...$this->secretValues, ...$this->credentials] as $secret) {
            if ($secret !== '') {
                $replacements[$secret] = '[redacted]';
                $replacements[rawurlencode($secret)] = '[redacted]';
            }
        }

        return strtr($text, $replacements);
    }

    /**
     * @param array<mixed> $values
     *
     * @return array<mixed>
     */
    public function redactArray(array $values): array
    {
        $redacted = [];

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $redacted[$key] = $this->redactArray($value);

                continue;
            }

            $redacted[$key] = is_string($key) && $this->isSecretKey($key)
                ? self::MASK
                : $this->redactText(is_string($value) ? $value : '');
        }

        return $redacted;
    }

    public const MASK = '***';

    private function isSecretKey(string $key): bool
    {
        $normalized = strtolower(str_replace(['-', ' ', '_'], '', $key));

        foreach (self::SECRET_KEY_PATTERNS as $pattern) {
            if (str_contains($normalized, str_replace('_', '', $pattern))) {
                return true;
            }
        }

        return false;
    }
}
