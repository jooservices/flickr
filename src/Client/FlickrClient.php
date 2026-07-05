<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Cache\CacheKeyResolver;
use JOOservices\Flickr\Cache\NullCache;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrSignerContract;
use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;
use JOOservices\Flickr\Contracts\Client\FlickrClientContract;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\Enums\CachePolicy;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Enums\ResponseFormat;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\RateLimitException;
use JOOservices\Flickr\Metadata\FlickrMethodDefinition;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Support\ParameterNormalizer;

final class FlickrClient implements FlickrClientContract
{
    public function __construct(
        private FlickrConfig $config,
        private FlickrTransportContract $transport,
        private FlickrSignerContract $signer,
        private FlickrTokenStoreContract $tokens,
        private FlickrMethodRegistry $registry,
        private FlickrResponseParser $parser = new FlickrResponseParser,
        private ParameterNormalizer $normalizer = new ParameterNormalizer,
        private FlickrCacheContract $cache = new NullCache,
        private CacheKeyResolver $cacheKeys = new CacheKeyResolver,
    ) {}

    public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
    {
        $options ??= new RequestOptionsData;
        $definition = $this->registry->find($method);
        $parameters = $this->prepareParameters($method, $parameters);
        $cacheKey = $this->cacheKey($definition, $parameters, $options);

        if ($cacheKey !== null) {
            $cached = $this->cache->get($cacheKey);

            if ($cached instanceof ApiResponseData) {
                return $cached;
            }
        }

        $parameters = $this->authenticate($definition, $parameters, $options);
        $raw = $this->transport->request(
            $definition->httpMethod->value,
            $this->config->restEndpoint,
            $this->transportOptions($definition, $parameters),
        );
        $this->checkRateLimit($raw);

        $response = $this->parser->parseApi($raw);

        if ($cacheKey !== null && $response->ok) {
            $this->cache->put($cacheKey, $response, $options->cacheTtl ?? $this->config->publicCacheTtlSeconds);
        }

        if (! $response->ok && $options->throwOnApiError) {
            $this->handleApiError($response);
        }

        return $response;
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function prepareParameters(string $method, array $parameters): array
    {
        $parameters = $this->normalizer->normalize($parameters);
        $parameters['method'] = $method;
        $parameters['api_key'] = $this->config->apiKey;
        $parameters['format'] = $this->config->responseFormat->value;

        if ($this->config->responseFormat === ResponseFormat::Json) {
            $parameters['nojsoncallback'] = 1;
        }

        return $parameters;
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private function authenticate(FlickrMethodDefinition $definition, array $parameters, RequestOptionsData $options): array
    {
        if (! $definition->requiresAuth && ! $options->authenticated) {
            return $parameters;
        }

        $token = $this->tokens->get();

        if ($token === null) {
            throw new AuthenticationException("Flickr method {$definition->name} requires an OAuth access token.");
        }

        return array_merge($parameters, $this->signer->sign(
            $definition->httpMethod->value,
            $this->config->restEndpoint,
            $parameters,
            $token->oauthToken,
            $token->oauthTokenSecret,
        ));
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, array<string, mixed>>
     */
    private function transportOptions(FlickrMethodDefinition $definition, array $parameters): array
    {
        return $definition->httpMethod === HttpMethod::Post
            ? ['form_params' => $parameters]
            : ['query' => $parameters];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function cacheKey(FlickrMethodDefinition $definition, array $parameters, RequestOptionsData $options): ?string
    {
        if (! $this->shouldUseCache($definition, $options)) {
            return null;
        }

        return $this->cacheKeys->resolve($definition->name, $parameters);
    }

    private function shouldUseCache(FlickrMethodDefinition $definition, RequestOptionsData $options): bool
    {
        if ($options->cache === CachePolicy::Disabled) {
            return false;
        }

        if ($definition->httpMethod !== HttpMethod::Get || $definition->requiresAuth || $options->authenticated) {
            return false;
        }

        if ($options->cache === CachePolicy::Enabled) {
            return true;
        }

        return $definition->cacheable;
    }

    private function checkRateLimit(RawResponseData $raw): void
    {
        if ($raw->statusCode !== 429) {
            return;
        }

        $retryAfter = $this->retryAfterHeader($raw->headers);

        throw new RateLimitException('Flickr rate limit exceeded.', $retryAfter);
    }

    private function handleApiError(ApiResponseData $response): void
    {
        $error = $response->error;
        $errCode = $error !== null ? $error->code : null;
        $errMsg = $error !== null ? $error->message : 'Flickr API request failed.';

        if ($errCode !== null && in_array($errCode, [96, 97, 98, 99], true)) {
            throw new AuthorizationException($errMsg, $errCode);
        }

        throw new ApiException($errMsg, $errCode);
    }

    /**
     * @param  array<string, list<string>>  $headers
     */
    private function retryAfterHeader(array $headers): ?int
    {
        foreach ($headers as $name => $values) {
            if (strcasecmp($name, 'Retry-After') === 0 && isset($values[0])) {
                return (int) $values[0];
            }
        }

        return null;
    }
}
