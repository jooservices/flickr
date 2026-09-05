<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Flickr\Client\FlickrRequestBuilder;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Contracts\Clock;
use JOOservices\Flickr\Contracts\FlickrTransport;
use JOOservices\Flickr\Contracts\TokenStore;
use InvalidArgumentException;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\RateLimitException;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Support\QueryString;

/**
 * Stateless OAuth 1.0a web-flow transaction: begin() issues the request
 * token and returns an immutable pending transaction the consumer must
 * persist server-side; complete() validates expiry/confirmation/binding and
 * exchanges it for an access token.
 */
final class OAuth1Authenticator
{
    public function __construct(
        private readonly FlickrTransport $transport,
        private readonly FlickrRequestBuilder $requests,
        private readonly OAuthRequestParamSigner $signer,
        private readonly TokenStore $tokens,
        private readonly Clock $clock,
        private readonly FlickrConfig $config,
    ) {
    }

    public function begin(AuthPermission $permission): PendingAuthorization
    {
        if ($permission === AuthPermission::None) {
            throw new InvalidArgumentException('An OAuth flow requires read, write or delete permission.');
        }

        $callback = $this->config->callbackUrl ?? 'oob';

        $parameters = $this->signer->signedParameters(
            HttpMethod::Post,
            FlickrEndpoints::OAUTH_REQUEST_TOKEN,
            ['oauth_callback' => $callback],
            $this->config->apiKey,
            $this->config->apiSecret,
        );

        $response = $this->transport->send(
            $this->requests->formPost(FlickrEndpoints::OAUTH_REQUEST_TOKEN, $parameters),
            new RequestOptions(allowRedirects: false),
        );

        $fields = $this->tokenFields($response);

        if (($fields['oauth_callback_confirmed'] ?? null) !== 'true') {
            throw new AuthenticationException('Flickr did not confirm the OAuth callback.');
        }

        $token = $this->requiredField($fields, 'oauth_token');
        $secret = $this->requiredField($fields, 'oauth_token_secret');

        return new PendingAuthorization(
            requestToken: $token,
            requestTokenSecret: $secret,
            permission: $permission,
            issuedAt: $this->clock->now(),
            authorizationUrl: sprintf(
                '%s?oauth_token=%s&perms=%s',
                FlickrEndpoints::OAUTH_AUTHORIZE,
                rawurlencode($token),
                $permission->value,
            ),
        );
    }

    public function complete(PendingAuthorization $pending, OAuthCallback $callback): AccessTokenData
    {
        if ($this->clock->now() - $pending->issuedAt > PendingAuthorization::LIFETIME_SECONDS) {
            throw new AuthenticationException('The OAuth pending authorization expired.');
        }

        if (hash_equals($pending->requestToken, $callback->token) === false) {
            throw new AuthenticationException('The OAuth callback token does not match the pending transaction.');
        }

        $parameters = $this->signer->signedParameters(
            HttpMethod::Post,
            FlickrEndpoints::OAUTH_ACCESS_TOKEN,
            ['oauth_verifier' => $callback->verifier],
            $this->config->apiKey,
            $this->config->apiSecret,
            $pending->requestToken,
            $pending->requestTokenSecret,
        );

        $response = $this->transport->send(
            $this->requests->formPost(FlickrEndpoints::OAUTH_ACCESS_TOKEN, $parameters),
            new RequestOptions(allowRedirects: false),
        );

        $fields = $this->tokenFields($response);
        $token = $this->requiredField($fields, 'oauth_token');
        $secret = $this->requiredField($fields, 'oauth_token_secret');

        $accessToken = new AccessTokenData($token, $secret, $pending->permission);
        $this->tokens->put($accessToken);

        return $accessToken;
    }

    /**
     * @return array<string, string|list<string>>
     */
    private function tokenFields(RawResponseData $response): array
    {
        if ($response->status === 429) {
            throw new RateLimitException('Flickr OAuth rate limit reached.', FlickrResponseParser::retryAfterSeconds($response));
        }

        if ($response->status < 200 || $response->status >= 300) {
            throw new AuthenticationException(sprintf('Flickr OAuth returned HTTP %d.', $response->status));
        }

        return QueryString::parseForm($response->body);
    }

    /**
     * @param array<string, mixed> $fields
     */
    private function requiredField(array $fields, string $name): string
    {
        $value = $fields[$name] ?? null;

        if (!is_string($value) || trim($value) === '') {
            throw new AuthenticationException(sprintf('Flickr OAuth response is missing "%s".', $name));
        }

        return $value;
    }
}
