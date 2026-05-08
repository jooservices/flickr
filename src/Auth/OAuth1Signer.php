<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrSignerContract;
use JOOservices\Flickr\Exceptions\ConfigurationException;
use JOOservices\Flickr\Support\ParameterNormalizer;
use JOOservices\Flickr\Support\QueryString;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;

final class OAuth1Signer implements FlickrSignerContract
{
    public function __construct(
        private FlickrConfig $config,
        private SignatureBaseStringBuilder $baseStringBuilder = new SignatureBaseStringBuilder,
        private ParameterNormalizer $normalizer = new ParameterNormalizer,
    ) {}

    public function sign(
        string $method,
        string $url,
        array $parameters = [],
        ?string $token = null,
        ?string $tokenSecret = null,
        ?string $nonce = null,
        ?int $timestamp = null,
    ): array {
        if ($this->config->apiSecret === '') {
            throw new ConfigurationException('OAuth signing requires an API secret.');
        }

        $oauth = [
            'oauth_consumer_key' => $this->config->apiKey,
            'oauth_nonce' => $nonce ?? bin2hex(random_bytes(16)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) ($timestamp ?? time()),
            'oauth_version' => '1.0',
        ];

        if ($token !== null && $token !== '') {
            $oauth['oauth_token'] = $token;
        }

        $signingParameters = array_merge(
            $this->normalizer->normalize($parameters),
            $oauth,
        );
        $baseString = $this->signatureBaseString($method, $url, $signingParameters);
        $key = QueryString::encode($this->config->apiSecret).'&'.QueryString::encode((string) $tokenSecret);
        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $baseString, $key, true));

        return $oauth;
    }

    public function signatureBaseString(string $method, string $url, array $parameters): string
    {
        return $this->baseStringBuilder->build($method, $url, $parameters);
    }
}
