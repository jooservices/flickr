<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\Clock;
use JOOservices\Flickr\Contracts\NonceGenerator;
use JOOservices\Flickr\Contracts\Signer;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Support\PercentEncoding;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;
use JOOservices\Flickr\Support\SensitiveDataRedactor;

/**
 * Builds the complete signed parameter set (request parameters plus
 * `oauth_*` entries including the signature) for one Flickr request.
 */
final class OAuthRequestParamSigner
{
    public function __construct(
        private readonly Signer $signer,
        private readonly NonceGenerator $nonces,
        private readonly Clock $clock,
        private readonly SignatureBaseStringBuilder $baseStrings,
        private readonly SensitiveDataRedactor $redactor,
    ) {
    }

    /**
     * @param array<string, string|list<string>> $requestParameters normalized values
     *
     * @return array<string, string|list<string>>
     */
    public function signedParameters(
        HttpMethod $verb,
        string $uri,
        array $requestParameters,
        string $consumerKey,
        string $consumerSecret,
        ?string $oauthToken = null,
        ?string $tokenSecret = null,
    ): array {
        $oauth = [
            'oauth_consumer_key' => $consumerKey,
            'oauth_nonce' => $this->nonces->generate(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => (string) $this->clock->now(),
            'oauth_version' => '1.0',
        ];

        if ($oauthToken !== null) {
            $oauth['oauth_token'] = $oauthToken;
        }

        foreach ($oauth as $value) {
            $this->registerSecretAndEncodedForm($value);
        }
        $this->redactor->registerSecret($consumerSecret);
        if ($tokenSecret !== null && $tokenSecret !== '') {
            $this->redactor->registerSecret($tokenSecret);
        }

        $all = [...$requestParameters, ...$oauth];
        $baseString = $this->baseStrings->build($verb, $uri, $all);
        $signature = $this->signer->sign(
            $baseString,
            OAuth1Signer::signingKey($consumerSecret, $tokenSecret ?? ''),
        );
        $this->registerSecretAndEncodedForm($signature);

        return [...$requestParameters, ...$oauth, 'oauth_signature' => $signature];
    }

    /**
     * A GET request carries these values percent-encoded in its query
     * string (see `QueryString::build()`); a base64 `oauth_signature` in
     * particular almost always contains `+`, `/`, or `=`. Register both
     * forms so a transport error embedding the request URI still gets
     * fully redacted regardless of which form appears in it.
     */
    private function registerSecretAndEncodedForm(string $value): void
    {
        $this->redactor->registerSecret($value);
        $this->redactor->registerSecret(PercentEncoding::encode($value));
    }
}
