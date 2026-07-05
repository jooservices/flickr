<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrAuthenticatorContract;
use JOOservices\Flickr\Contracts\Auth\FlickrSignerContract;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Auth\RequestTokenData;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Support\UrlBuilder;

final class OAuth1Authenticator implements FlickrAuthenticatorContract
{
    /**
     * @var array<string, string>
     */
    private array $requestTokenSecrets = [];

    public function __construct(
        private FlickrConfig $config,
        private FlickrSignerContract $signer,
        private FlickrTransportContract $transport,
        private UrlBuilder $urlBuilder = new UrlBuilder,
    ) {}

    public function requestToken(AuthPermission $permission = AuthPermission::Read): RequestTokenData
    {
        $parameters = [
            'oauth_callback' => $this->config->callbackUrl ?? 'oob',
        ];

        $oauth = $this->signer->sign('GET', $this->config->requestTokenEndpoint, $parameters);
        $response = $this->transport->request('GET', $this->config->requestTokenEndpoint, [
            'query' => array_merge($parameters, $oauth),
        ]);

        $data = $this->parseTokenResponse($response->body);

        $requestToken = new RequestTokenData(
            oauthToken: $this->requireValue($data, 'oauth_token'),
            oauthTokenSecret: $this->requireValue($data, 'oauth_token_secret'),
            oauthCallbackConfirmed: ($data['oauth_callback_confirmed'] ?? 'false') === 'true',
        );
        $this->requestTokenSecrets[$requestToken->oauthToken] = $requestToken->oauthTokenSecret;

        return $requestToken;
    }

    public function authorizationUrl(RequestTokenData $requestToken, AuthPermission $permission): string
    {
        return $this->urlBuilder->withQuery($this->config->authorizeEndpoint, [
            'oauth_token' => $requestToken->oauthToken,
            'perms' => $permission->value,
        ]);
    }

    public function accessToken(string $oauthToken, string $oauthVerifier, ?string $oauthTokenSecret = null): AccessTokenData
    {
        if (trim($oauthToken) === '' || trim($oauthVerifier) === '') {
            throw new AuthenticationException('OAuth token and verifier are required.');
        }

        $parameters = ['oauth_verifier' => $oauthVerifier];
        $oauth = $this->signer->sign(
            'GET',
            $this->config->accessTokenEndpoint,
            $parameters,
            $oauthToken,
            $oauthTokenSecret ?? $this->requestTokenSecrets[$oauthToken] ?? null,
        );
        $response = $this->transport->request('GET', $this->config->accessTokenEndpoint, [
            'query' => array_merge($parameters, $oauth),
        ]);
        $data = $this->parseTokenResponse($response->body);

        return new AccessTokenData(
            oauthToken: $this->requireValue($data, 'oauth_token'),
            oauthTokenSecret: $this->requireValue($data, 'oauth_token_secret'),
            userNsid: $data['user_nsid'] ?? null,
            username: $data['username'] ?? null,
            fullname: $data['fullname'] ?? null,
        );
    }

    /**
     * @return array<string, string>
     */
    private function parseTokenResponse(string $body): array
    {
        parse_str($body, $data);

        if ($data === []) {
            throw new AuthenticationException('Flickr returned an empty OAuth response.');
        }

        /** @var array<string, string> $data */
        return $data;
    }

    /**
     * @param  array<string, string>  $data
     */
    private function requireValue(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new AuthenticationException("Flickr OAuth response is missing {$key}.");
        }

        return $value;
    }
}
