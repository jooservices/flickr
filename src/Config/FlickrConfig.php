<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Config;

use JOOservices\Flickr\Exceptions\ConfigurationException;

final class FlickrConfig
{
    public const DEFAULT_USER_AGENT = 'jooservices-flickr/4.0 (+https://github.com/jooservices/flickr)';

    public function __construct(
        public readonly string $apiKey,
        public readonly string $apiSecret,
        public readonly ?string $callbackUrl = null,
        public readonly string $userAgent = self::DEFAULT_USER_AGENT,
        public readonly int $cacheTtl = 600,
    ) {
        $this->assertFilled('API key', $apiKey);
        $this->assertFilled('API secret', $apiSecret);

        foreach (['user agent' => $userAgent, 'callback URL' => $callbackUrl] as $label => $value) {
            if ($value !== null && preg_match('/[\r\n\x00-\x1F\x7F]/', $value) === 1) {
                throw new ConfigurationException(sprintf('%s must not contain control characters.', ucfirst($label)));
            }
        }

        if ($callbackUrl !== null) {
            $this->assertValidCallbackUrl($callbackUrl);
        }

        if ($cacheTtl < 0) {
            throw new ConfigurationException('Cache TTL must not be negative.');
        }
    }

    private function assertFilled(string $label, string $value): void
    {
        if (trim($value) === '') {
            throw new ConfigurationException(sprintf('%s must not be blank.', $label));
        }
    }

    private function assertValidCallbackUrl(string $callbackUrl): void
    {
        if (filter_var($callbackUrl, FILTER_VALIDATE_URL) === false || parse_url($callbackUrl, PHP_URL_SCHEME) !== 'https') {
            throw new ConfigurationException('Callback URL must be an absolute HTTPS URL.');
        }

        if (parse_url($callbackUrl, PHP_URL_USER) !== null) {
            throw new ConfigurationException('Callback URL must not contain embedded userinfo.');
        }
    }
}
