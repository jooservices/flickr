<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Api\RawCallOptions;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Enums\AuthenticationMode;
use JOOservices\Flickr\Auth\AccessTokenData;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JOOservices\Flickr\Exceptions\RateLimitException;
use JOOservices\Flickr\Exceptions\UnavailableMethodException;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Tests\Support\FakeTransport;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ApiClientPipelineTest extends TestCase
{
    private FakeTransport $transport;

    private InMemoryTokenStore $tokens;

    protected function setUp(): void
    {
        $this->transport = new FakeTransport();
        $this->tokens = new InMemoryTokenStore();
    }

    public function testUnknownMethodSuggestsClosestRegistryName(): void
    {
        $this->expectExceptionMessageMatches('/Did you mean "flickr\.photos\.search"\?/');

        PipelineFactory::api($this->transport)->call('flickr.photos.serch');
    }

    public function testCanonicalParametersAlwaysWin(): void
    {
        $this->transport->queue(new RawResponseData(200, [], '{"stat":"ok"}'));

        $client = PipelineFactory::clientWithTokens($this->transport, $this->tokens);
        $definition = (new FlickrMethodRegistry())->find('flickr.photos.search');
        self::assertNotNull($definition);

        $client->call($definition, [
            'method' => 'evil',
            'api_key' => 'evil',
            'format' => 'xml',
            'nojsoncallback' => '0',
            'text' => 'sunset',
        ], new ApiCallOptions());

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);

        self::assertSame('flickr.photos.search', $dispatched['method']);
        self::assertSame('test-api-key', $dispatched['api_key']);
        self::assertSame('json', $dispatched['format']);
        self::assertSame('1', $dispatched['nojsoncallback']);
        self::assertSame('sunset', $dispatched['text']);
    }

    public function testUnauthenticatedModeRejectsRequiredAuthMethodBeforeSend(): void
    {
        $definition = (new FlickrMethodRegistry())->find('flickr.favorites.add');
        self::assertNotNull($definition);

        $this->expectException(AuthenticationException::class);
        $client = PipelineFactory::clientWithTokens($this->transport, $this->tokens);
        $client->call($definition, ['photo_id' => '1'], new ApiCallOptions(mode: AuthenticationMode::Unauthenticated));
    }

    public function testAuthenticatedModeWithoutTokenFailsBeforeSend(): void
    {
        $definition = (new FlickrMethodRegistry())->find('flickr.favorites.add');
        self::assertNotNull($definition);

        $this->expectException(AuthenticationException::class);
        $client = PipelineFactory::clientWithTokens($this->transport, $this->tokens);
        $client->call($definition, ['photo_id' => '1'], new ApiCallOptions());
    }

    public function testInsufficientTokenPermissionFailsBeforeSend(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Read));
        $definition = (new FlickrMethodRegistry())->find('flickr.photos.delete');
        self::assertNotNull($definition);

        try {
            PipelineFactory::clientWithTokens($this->transport, $this->tokens)
                ->call($definition, ['photo_id' => '1'], new ApiCallOptions());
            self::fail('Expected AuthorizationException.');
        } catch (AuthorizationException) {
            self::assertSame(0, $this->transport->sentCount());
        }
    }

    public function testAutomaticSigningAddsOAuthParametersOnSend(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Delete));
        $this->transport->queue(new RawResponseData(200, [], '{"stat":"ok"}'));

        $definition = (new FlickrMethodRegistry())->find('flickr.photos.delete');
        self::assertNotNull($definition);

        PipelineFactory::clientWithTokens($this->transport, $this->tokens)
            ->call($definition, ['photo_id' => '1'], new ApiCallOptions());

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);

        self::assertArrayHasKey('oauth_signature', $dispatched);
        self::assertArrayHasKey('oauth_consumer_key', $dispatched);
        self::assertArrayHasKey('oauth_token', $dispatched);
        self::assertSame('1', $dispatched['photo_id']);
        self::assertSame(1, $this->transport->sentCount());
    }

    public function testCacheablePublicGetIsServedFromCacheOnce(): void
    {
        $this->transport->queue(new RawResponseData(200, [], '{"stat":"ok","photos":{"page":1,"pages":1,"total":"0","photo":[]}}'));

        $api = PipelineFactory::api($this->transport, ['cache' => true]);
        $first = $api->call('flickr.photos.search', ['text' => 'sunset']);
        $second = $api->call('flickr.photos.search', ['text' => 'sunset']);

        self::assertSame(1, $this->transport->sentCount());
        self::assertTrue($first->ok);
        self::assertTrue($second->ok);
    }

    public function testBypassCacheForcesSecondNetworkCall(): void
    {
        $body = '{"stat":"ok","photos":{"page":1,"pages":1,"total":"0","photo":[]}}';
        $this->transport->queue(new RawResponseData(200, [], $body), new RawResponseData(200, [], $body));

        $api = PipelineFactory::api($this->transport, ['cache' => true]);
        $api->call('flickr.photos.search', ['text' => 'sunset']);
        $api->call('flickr.photos.search', ['text' => 'sunset'], new ApiCallOptions(bypassCache: true));

        self::assertSame(2, $this->transport->sentCount());
    }

    public function testPostMutationIsNeverCached(): void
    {
        $this->tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Delete));
        $okBody = '{"stat":"ok"}';
        $this->transport->queue(
            new RawResponseData(200, [], $okBody),
            new RawResponseData(200, [], $okBody),
        );

        $definition = (new FlickrMethodRegistry())->find('flickr.photos.delete');
        self::assertNotNull($definition);

        $client = PipelineFactory::clientWithTokens($this->transport, $this->tokens);
        $client->call($definition, ['photo_id' => '9'], new ApiCallOptions());
        $client->call($definition, ['photo_id' => '9'], new ApiCallOptions());

        self::assertSame(2, $this->transport->sentCount());
    }

    public function testStatFailReturnsEnvelopeByDefaultAndThrowsWhenRequested(): void
    {
        $failBody = '{"stat":"fail","code":100,"message":"Invalid API Key"}';

        $this->transport->queue(new RawResponseData(200, [], $failBody), new RawResponseData(200, [], $failBody));

        $definition = (new FlickrMethodRegistry())->find('flickr.cameras.getBrands');
        self::assertNotNull($definition);
        $client = PipelineFactory::client($this->transport);

        $envelope = $client->call($definition, [], new ApiCallOptions());

        self::assertFalse($envelope->ok);
        self::assertNotNull($envelope->error);
        self::assertSame('100', $envelope->error->code);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid API Key');
        $client->call($definition, [], new ApiCallOptions(throwOnApiError: true));
    }

    /**
     * @return iterable<string, array{0: string, 1: class-string<Throwable>}>
     */
    public static function provideErrorCodeMap(): iterable
    {
        yield 'auth 98' => ['98', AuthenticationException::class];
        yield 'permissions 99' => ['99', AuthorizationException::class];
    }

    /**
     * @param class-string<Throwable> $exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideErrorCodeMap')]
    public function testErrorMapThrowsTypedExceptions(string $code, string $exception): void
    {
        $this->transport->queue(new RawResponseData(200, [], sprintf('{"stat":"fail","code":"%s","message":"nope"}', $code)));

        $definition = (new FlickrMethodRegistry())->find('flickr.cameras.getBrands');
        self::assertNotNull($definition);

        $this->expectException($exception);
        PipelineFactory::client($this->transport)->call($definition, [], new ApiCallOptions(throwOnApiError: true));
    }

    public function testOnlyServiceUnavailableIsMarkedRetryable(): void
    {
        $definition = (new FlickrMethodRegistry())->find('flickr.cameras.getBrands');
        self::assertNotNull($definition);

        $this->transport->queue(new RawResponseData(200, [], '{"stat":"fail","code":"105","message":"Service currently unavailable"}'));

        try {
            PipelineFactory::client($this->transport)->call($definition, [], new ApiCallOptions(throwOnApiError: true));
            self::fail('Expected an ApiException.');
        } catch (ApiException $error) {
            self::assertTrue($error->retryable);
            self::assertSame('105', $error->flickrCode);
        }

        $this->transport->queue(new RawResponseData(200, [], '{"stat":"fail","code":"1","message":"Photo not found"}'));

        try {
            PipelineFactory::client($this->transport)->call($definition, [], new ApiCallOptions(throwOnApiError: true));
            self::fail('Expected an ApiException.');
        } catch (ApiException $error) {
            self::assertFalse($error->retryable);
            self::assertSame('1', $error->flickrCode);
        }
    }

    public function test429MapsToRateLimitExceptionWithRetryAfter(): void
    {
        $this->transport->queue(new RawResponseData(429, ['retry-after' => ['42']], '{}'));
        $this->expectException(RateLimitException::class);

        try {
            PipelineFactory::api($this->transport)->call('flickr.cameras.getBrands');
        } catch (RateLimitException $error) {
            self::assertSame(42, $error->retryAfterSeconds);
            throw $error;
        }
    }

    public function test429WithoutNumericRetryAfterKeepsNullDelay(): void
    {
        $this->transport->queue(new RawResponseData(429, ['retry-after' => ['Wed, 21 Oct 2026 07:28:00 GMT']], '{}'));

        try {
            PipelineFactory::api($this->transport)->call('flickr.cameras.getBrands');
            self::fail('Expected RateLimitException.');
        } catch (RateLimitException $error) {
            self::assertNull($error->retryAfterSeconds);
        }
    }

    public function testNonSuccessfulHttpResponseIsNotCachedAsAnApiSuccess(): void
    {
        $this->transport->queue(
            new RawResponseData(500, [], '{"stat":"ok"}'),
            new RawResponseData(200, [], '{"stat":"ok"}'),
        );
        $api = PipelineFactory::api($this->transport, ['cache' => true]);

        try {
            $api->call('flickr.cameras.getBrands');
            self::fail('Expected InvalidResponseException.');
        } catch (InvalidResponseException) {
            self::assertSame(1, $this->transport->sentCount());
        }

        self::assertTrue($api->call('flickr.cameras.getBrands')->ok);
        self::assertSame(2, $this->transport->sentCount());
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function provideBrokenBodies(): iterable
    {
        yield 'malformed json' => ['{"stat":'];
        yield 'scalar json' => ['123'];
        yield 'empty body' => [''];
        yield 'missing stat' => ['{"photos":{}}'];
        yield 'non-string stat' => ['{"stat":[]}'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideBrokenBodies')]
    public function testBrokenBodiesMapToInvalidResponse(string $body): void
    {
        $this->transport->queue(new RawResponseData(200, [], $body));
        $definition = (new FlickrMethodRegistry())->find('flickr.cameras.getBrands');
        self::assertNotNull($definition);

        $this->expectException(InvalidResponseException::class);
        PipelineFactory::client($this->transport)->call($definition, [], new ApiCallOptions());
    }

    public function testBomPrefixedJsonParsesFine(): void
    {
        $this->transport->queue(new RawResponseData(200, [], "\xEF\xBB\xBF" . '{"stat":"ok"}'));
        $definition = (new FlickrMethodRegistry())->find('flickr.cameras.getBrands');
        self::assertNotNull($definition);

        $response = PipelineFactory::client($this->transport)->call($definition, [], new ApiCallOptions());

        self::assertTrue($response->ok);
    }

    public function testUnavailableLegacyMethodFailsBeforeAnyNetworkTraffic(): void
    {
        $definition = (new FlickrMethodRegistry())->find('flickr.auth.getFrob');
        self::assertNotNull($definition);
        self::assertFalse($definition->available);

        try {
            PipelineFactory::client($this->transport)->call($definition, [], new ApiCallOptions());
            self::fail('Expected UnavailableMethodException.');
        } catch (UnavailableMethodException) {
            self::assertSame(0, $this->transport->sentCount());
        }
    }

    public function testRawCallRequiresExplicitNonAutomaticMode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RawCallOptions(AuthenticationMode::Automatic);
    }

    public function testRawCallsAreNeverCachedAndRequireExplicitVerb(): void
    {
        $ok = '{"stat":"ok"}';
        $this->transport->queue(new RawResponseData(200, [], $ok), new RawResponseData(200, [], $ok));
        $api = PipelineFactory::api($this->transport, ['cache' => true]);

        $options = new RawCallOptions(AuthenticationMode::Unauthenticated);
        $api->raw('flickr.future.method', HttpMethod::Get, $options, ['query' => 'a']);
        $api->raw('flickr.future.method', HttpMethod::Get, $options, ['query' => 'a']);

        self::assertSame(2, $this->transport->sentCount());
    }
}
