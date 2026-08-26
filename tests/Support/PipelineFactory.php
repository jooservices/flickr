<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Support;

use JOOservices\Flickr\Api\Api;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Auth\OAuthRequestParamSigner;
use JOOservices\Flickr\Cache\Psr16Cache;
use JOOservices\Flickr\Client\ApiClient;
use JOOservices\Flickr\Client\FlickrRequestBuilder;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Support\QueryString;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache as SymfonyPsr16;

final class PipelineFactory
{
    public static function redactor(): SensitiveDataRedactor
    {
        $redactor = new SensitiveDataRedactor();
        $redactor->registerSecret('test-api-key');
        $redactor->registerSecret('test-api-secret');

        return $redactor;
    }

    /**
     * @param array{cache?: bool} $options
     */
    public static function client(FakeTransport $transport, array $options = []): ApiClient
    {
        return self::clientWithTokens($transport, new InMemoryTokenStore(), $options);
    }

    /**
     * @param array{cache?: bool} $options
     */
    public static function clientWithTokens(FakeTransport $transport, InMemoryTokenStore $tokens, array $options = []): ApiClient
    {
        $redactor = self::redactor();
        $psr17 = \JOOservices\Flickr\Client\Psr17Factories::nyholm();

        $fixedNonce = new class implements \JOOservices\Flickr\Contracts\NonceGenerator {
            public function generate(): string
            {
                return 'fixed-nonce';
            }
        };

        $signer = new OAuthRequestParamSigner(
            new OAuth1Signer(),
            $fixedNonce,
            new \JOOservices\Flickr\Tests\Support\MutableClock(),
            new SignatureBaseStringBuilder(),
            $redactor,
        );

        $cache = ($options['cache'] ?? false)
            ? new Psr16Cache(new SymfonyPsr16(new ArrayAdapter()))
            : new \JOOservices\Flickr\Cache\NullCache();

        return new ApiClient(
            new FlickrRequestBuilder($psr17, 'jooservices-test'),
            $transport,
            new FlickrResponseParser($redactor),
            $signer,
            $tokens,
            $cache,
            'test-api-key',
            'test-api-secret',
            600,
        );
    }

    /**
     * @param array{cache?: bool} $options
     */
    public static function api(FakeTransport $transport, array $options = []): Api
    {
        return new Api(self::client($transport, $options), new FlickrMethodRegistry());
    }

    /**
     * Like {@see self::api()} but backed by a caller-supplied token store, so
     * a test can put an access token in scope and see it honored both by the
     * generic `Api::call()` path and by another collaborator (e.g.
     * `UploadService`) constructed against the same store.
     *
     * @param array{cache?: bool} $options
     */
    public static function apiWithTokens(FakeTransport $transport, InMemoryTokenStore $tokens, array $options = []): Api
    {
        return new Api(self::clientWithTokens($transport, $tokens, $options), new FlickrMethodRegistry());
    }

    public static function config(): FlickrConfig
    {
        return new FlickrConfig(apiKey: 'test-api-key', apiSecret: 'test-api-secret');
    }

    /**
     * Decodes the dispatched parameters of a captured PSR-7 request.
     *
     * @return array<string, string|list<string>>
     */
    public static function dispatchedParameters(\Psr\Http\Message\RequestInterface $request): array
    {
        return $request->getMethod() === 'GET'
            ? QueryString::parseForm($request->getUri()->getQuery())
            : QueryString::parseForm((string) $request->getBody());
    }
}
