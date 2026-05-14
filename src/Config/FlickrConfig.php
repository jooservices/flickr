<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Config;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Enums\ResponseFormat;
use JOOservices\Flickr\Exceptions\ConfigurationException;

final class FlickrConfig extends Dto
{
    public function __construct(
        public string $apiKey,
        public string $apiSecret,
        public ?string $callbackUrl = null,
        public ResponseFormat $responseFormat = ResponseFormat::Json,
        public string $restEndpoint = 'https://www.flickr.com/services/rest',
        public string $requestTokenEndpoint = 'https://www.flickr.com/services/oauth/request_token',
        public string $authorizeEndpoint = 'https://www.flickr.com/services/oauth/authorize',
        public string $accessTokenEndpoint = 'https://www.flickr.com/services/oauth/access_token',
        public string $uploadEndpoint = 'https://up.flickr.com/services/upload',
        public string $replaceEndpoint = 'https://up.flickr.com/services/replace',
        public int $timeoutSeconds = 30,
        public int $retryTimes = 0,
        public string $userAgent = 'JOOservices Flickr SDK/1.0',
        public int $publicCacheTtlSeconds = 300,
    ) {
        if (trim($this->apiKey) === '') {
            throw new ConfigurationException('Flickr API key is required.');
        }

        if (trim($this->apiSecret) === '') {
            throw new ConfigurationException('Flickr API secret is required.');
        }

        if ($this->timeoutSeconds < 1) {
            throw new ConfigurationException('Timeout must be at least 1 second.');
        }

        if ($this->retryTimes < 0) {
            throw new ConfigurationException('Retry times cannot be negative.');
        }

        if ($this->publicCacheTtlSeconds < 1) {
            throw new ConfigurationException('Public cache TTL must be at least 1 second.');
        }
    }
}
