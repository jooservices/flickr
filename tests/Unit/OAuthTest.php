<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\OAuth1Authenticator;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Auth\OAuthCallback;
use JOOservices\Flickr\Auth\OAuthRequestParamSigner;
use JOOservices\Flickr\Auth\PendingAuthorization;
use JOOservices\Flickr\Client\FlickrRequestBuilder;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Tests\Support\FakeTransport;
use JOOservices\Flickr\Tests\Support\MutableClock;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use JOOservices\Flickr\Support\PercentEncoding;
use JOOservices\Flickr\Support\QueryString;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;
use PHPUnit\Framework\TestCase;

final class OAuthTest extends TestCase
{
    private FakeTransport $transport;

    private InMemoryTokenStore $tokens;

    private MutableClock $clock;

    protected function setUp(): void
    {
        $this->transport = new FakeTransport();
        $this->tokens = new InMemoryTokenStore();
        $this->clock = new MutableClock();
    }

    private function authenticator(?FlickrConfig $config = null): OAuth1Authenticator
    {
        $redactor = PipelineFactory::redactor();

        $fixedNonce = new class implements \JOOservices\Flickr\Contracts\NonceGenerator {
            public function generate(): string
            {
                return 'fixed-nonce';
            }
        };

        $signer = new OAuthRequestParamSigner(
            new OAuth1Signer(),
            $fixedNonce,
            $this->clock,
            new SignatureBaseStringBuilder(),
            $redactor,
        );

        return new OAuth1Authenticator(
            $this->transport,
            new FlickrRequestBuilder(\JOOservices\Flickr\Client\Psr17Factories::nyholm(), 'jooservices-test'),
            $signer,
            $this->tokens,
            $this->clock,
            $config ?? PipelineFactory::config(),
        );
    }

    public function testHmacSha1MatchesIndependentlyConstructedVector(): void
    {
        $baseString = 'POST&https%3A%2F%2Fwww.flickr.com%2Fservices%2Foauth%2Frequest_token'
            . '&oauth_callback%3Doob%26oauth_consumer_key%3Dkey%26oauth_nonce%3Dabc'
            . '%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1700000000%26oauth_version%3D1.0';
        $key = rawurlencode('consumer-secret') . '&';

        $expected = base64_encode(hash_hmac('sha1', $baseString, $key, true));

        self::assertSame($expected, (new OAuth1Signer())->sign($baseString, $key));
        self::assertSame($key, OAuth1Signer::signingKey('consumer-secret', null));
        self::assertSame($key . rawurlencode('token-secret'), OAuth1Signer::signingKey('consumer-secret', 'token-secret'));
    }

    public function testSignedParametersRegisterThePercentEncodedSignatureToo(): void
    {
        $redactor = PipelineFactory::redactor();
        $signer = new OAuthRequestParamSigner(
            new OAuth1Signer(),
            new \JOOservices\Flickr\Support\RandomNonceGenerator(),
            $this->clock,
            new SignatureBaseStringBuilder(),
            $redactor,
        );

        $signed = $signer->signedParameters(
            HttpMethod::Get,
            FlickrEndpoints::REST,
            [],
            'test-api-key',
            'test-api-secret',
            'tok',
            'sec',
        );

        $signature = $signed['oauth_signature'];
        self::assertIsString($signature);
        // A base64-encoded 20-byte HMAC-SHA1 digest always carries exactly
        // one "=" padding byte, so it always needs percent-encoding.
        self::assertStringEndsWith('=', $signature);

        $requestUri = FlickrEndpoints::REST . '?' . QueryString::build($signed);
        self::assertStringContainsString(PercentEncoding::encode($signature), $requestUri);

        $transportErrorMessage = 'cURL error 6: Could not resolve host for ' . $requestUri;
        $redacted = $redactor->redactText($transportErrorMessage);

        self::assertStringNotContainsString(PercentEncoding::encode($signature), $redacted);
        self::assertStringContainsString('[redacted]', $redacted);
    }

    public function testBaseStringBuilderNormalizesPortsAndSorting(): void
    {
        $builder = new SignatureBaseStringBuilder();

        self::assertSame(
            'https://www.flickr.com/services/rest/',
            SignatureBaseStringBuilder::normalizeBaseUri('HTTPS://WWW.Flickr.COM:443/services/rest/'),
        );

        self::assertSame('https://www.flickr.com:8443/x', SignatureBaseStringBuilder::normalizeBaseUri('https://www.flickr.com:8443/x'));
    }

    public function testBeginReturnsImmutablePendingTransaction(): void
    {
        $this->transport->queue(new RawResponseData(
            200,
            [],
            'oauth_callback_confirmed=true&oauth_token=req-token&oauth_token_secret=req-secret',
        ));

        $pending = $this->authenticator()->begin(AuthPermission::Write);

        self::assertSame('req-token', $pending->requestToken);
        self::assertSame('req-secret', $pending->requestTokenSecret);
        self::assertSame(AuthPermission::Write, $pending->permission);
        self::assertStringStartsWith(FlickrEndpoints::OAUTH_AUTHORIZE . '?oauth_token=req-token&perms=write', $pending->authorizationUrl);

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);

        self::assertSame('oob', $dispatched['oauth_callback']);
        self::assertArrayHasKey('oauth_signature', $dispatched);
    }

    public function testBeginUsesConfiguredCallbackUrl(): void
    {
        $this->transport->queue(new RawResponseData(
            200,
            [],
            'oauth_callback_confirmed=true&oauth_token=t&oauth_token_secret=s',
        ));

        $config = new FlickrConfig(apiKey: 'test-api-key', apiSecret: 'test-api-secret', callbackUrl: 'https://app.test/oauth/callback');
        $this->authenticator($config)->begin(AuthPermission::Read);

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);
        self::assertSame('https://app.test/oauth/callback', $dispatched['oauth_callback']);
    }

    public function testBeginFailsWhenCallbackNotConfirmed(): void
    {
        $this->transport->queue(new RawResponseData(200, [], 'oauth_callback_confirmed=false&oauth_token=t&oauth_token_secret=s'));

        $this->expectException(AuthenticationException::class);
        $this->authenticator()->begin(AuthPermission::Read);
    }

    public function testBeginFailsWhenTokenFieldsMissing(): void
    {
        $this->transport->queue(new RawResponseData(200, [], 'oauth_callback_confirmed=true'));

        $this->expectException(AuthenticationException::class);
        $this->authenticator()->begin(AuthPermission::Read);
    }

    public function testCompleteRejectsExpiredTransaction(): void
    {
        $pending = new PendingAuthorization('t', 's', AuthPermission::Read, $this->clock->now() - PendingAuthorization::LIFETIME_SECONDS - 1, 'url');

        $this->expectException(AuthenticationException::class);
        $this->authenticator()->complete($pending, new OAuthCallback('t', '123'));
    }

    public function testCompleteRejectsForeignCallbackToken(): void
    {
        $pending = new PendingAuthorization('real-token', 's', AuthPermission::Read, $this->clock->now(), 'url');

        try {
            $this->authenticator()->complete($pending, new OAuthCallback('evil-token', '123'));
            self::fail('Expected AuthenticationException.');
        } catch (AuthenticationException) {
            self::assertSame(0, $this->transport->sentCount());
        }
    }

    public function testCompleteExchangesAndStoresAccessToken(): void
    {
        $this->transport->queue(new RawResponseData(
            200,
            [],
            'oauth_token=access-token&oauth_token_secret=access-secret&user_nsid=123',
        ));

        $pending = new PendingAuthorization('req-token', 'req-secret', AuthPermission::Write, $this->clock->now(), 'url');
        $token = $this->authenticator()->complete($pending, new OAuthCallback('req-token', 'verifier-9'));

        self::assertSame('access-token', $token->token);
        self::assertSame('access-secret', $token->tokenSecret);
        self::assertSame(AuthPermission::Write, $this->tokens->get()?->permission);

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);

        self::assertSame('verifier-9', $dispatched['oauth_verifier']);
        self::assertSame('req-token', $dispatched['oauth_token']);
    }
}
