<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Auth\AccessTokenData;
use JOOservices\Flickr\Auth\OAuthRequestParamSigner;
use JOOservices\Flickr\Cache\CacheKeyResolver;
use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Contracts\FlickrCache;
use JOOservices\Flickr\Contracts\FlickrTransport;
use JOOservices\Flickr\Contracts\TokenStore;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Enums\AuthenticationMode;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\UnavailableMethodException;
use JOOservices\Flickr\Metadata\FlickrMethodDefinition;
use JOOservices\Flickr\Support\ParameterNormalizer;

/**
 * The single REST pipeline: registry policy → normalize → cache → auth/sign →
 * build → transport → parse/error map → cache write. No other code path may
 * send a Flickr REST request.
 */
final class ApiClient
{
    private const CANONICAL_KEYS = ['method', 'api_key', 'format', 'nojsoncallback'];

    public function __construct(
        private readonly FlickrRequestBuilder $requests,
        private readonly FlickrTransport $transport,
        private readonly FlickrResponseParser $parser,
        private readonly OAuthRequestParamSigner $signer,
        private readonly TokenStore $tokens,
        private readonly FlickrCache $cache,
        private readonly string $apiKey,
        private readonly string $apiSecret,
        private readonly int $cacheTtl,
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function call(FlickrMethodDefinition $definition, array $parameters, ApiCallOptions $options): ApiResponseData
    {
        $this->assertAvailable($definition);

        $mode = $this->resolveMode($definition, $options);
        $this->assertAuthenticationPolicy($definition, $mode);

        $normalized = $this->canonicalParameters($parameters, $definition->name);
        $cacheKey = $this->cacheKeyIfEligible($definition, $mode, $options, $normalized);

        if ($cacheKey !== null) {
            $cached = $this->cache->get($cacheKey);

            if ($cached !== null) {
                return $cached;
            }
        }

        $response = $this->dispatch($definition, $mode, $normalized);

        return $this->finish($options, $response, $cacheKey);
    }

    private function assertAvailable(FlickrMethodDefinition $definition): void
    {
        if ($definition->available === false) {
            throw new UnavailableMethodException($definition->name, $definition->deprecationReason);
        }
    }

    private function resolveMode(FlickrMethodDefinition $definition, ApiCallOptions $options): AuthenticationMode
    {
        if ($options->mode !== AuthenticationMode::Automatic) {
            return $options->mode;
        }

        return $definition->requiresAuth() ? AuthenticationMode::Authenticated : AuthenticationMode::Unauthenticated;
    }

    private function assertAuthenticationPolicy(FlickrMethodDefinition $definition, AuthenticationMode $mode): void
    {
        if ($mode === AuthenticationMode::Unauthenticated && $definition->requiresAuth()) {
            throw new AuthenticationException(sprintf(
                'Flickr method "%s" requires %s authentication.',
                $definition->name,
                $definition->permission->value,
            ));
        }
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, string|list<string>>
     */
    private function canonicalParameters(array $parameters, string $method): array
    {
        $normalized = ParameterNormalizer::normalize($parameters);

        foreach (self::CANONICAL_KEYS as $canonical) {
            unset($normalized[$canonical]);
        }

        $normalized['method'] = $method;
        $normalized['api_key'] = $this->apiKey;
        $normalized['format'] = 'json';
        $normalized['nojsoncallback'] = '1';

        return $normalized;
    }

    /**
     * @param array<string, string|list<string>> $normalized
     */
    private function cacheKeyIfEligible(
        FlickrMethodDefinition $definition,
        AuthenticationMode $mode,
        ApiCallOptions $options,
        array $normalized,
    ): ?string {
        $eligible = $definition->cacheable
            && $mode === AuthenticationMode::Unauthenticated
            && $definition->httpMethod === HttpMethod::Get
            && $options->bypassCache === false
            && $this->cacheTtl > 0;

        return $eligible ? CacheKeyResolver::key($definition->name, $normalized) : null;
    }

    /**
     * @param array<string, string|list<string>> $normalized
     */
    private function dispatch(
        FlickrMethodDefinition $definition,
        AuthenticationMode $mode,
        array $normalized,
    ): RawResponseData {
        if ($mode === AuthenticationMode::Authenticated) {
            $token = $this->authorisedToken($definition);
            $normalized = $this->signer->signedParameters(
                $definition->httpMethod,
                FlickrEndpoints::REST,
                $normalized,
                $this->apiKey,
                $this->apiSecret,
                $token->token,
                $token->tokenSecret,
            );
        }

        return $this->transport->send(
            $this->requests->rest($definition->httpMethod, $normalized),
            new RequestOptions(allowRedirects: false),
        );
    }

    private function authorisedToken(FlickrMethodDefinition $definition): AccessTokenData
    {
        $token = $this->tokens->get();

        if ($token === null) {
            throw new AuthenticationException('The call requires authentication but no access token is stored.');
        }

        if ($token->permission->satisfies($definition->permission) === false) {
            throw new AuthorizationException(sprintf(
                'Stored token permission "%s" is insufficient for "%s" (%s required).',
                $token->permission->value,
                $definition->name,
                $definition->permission->value,
            ));
        }

        return $token;
    }

    private function finish(ApiCallOptions $options, RawResponseData $response, ?string $cacheKey): ApiResponseData
    {
        $result = $this->parser->parse($response);

        if ($result->ok === false && $options->throwOnApiError) {
            assert($result->error !== null);
            throw FlickrErrorCodeMap::throwFor($result->error);
        }

        if ($cacheKey !== null && $result->ok) {
            $this->cache->put($cacheKey, $result, $this->cacheTtl);
        }

        return $result;
    }
}
