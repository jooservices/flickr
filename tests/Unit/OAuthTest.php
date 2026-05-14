<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\FileTokenStore;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\NullTokenStore;
use JOOservices\Flickr\Auth\OAuth1Authenticator;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\TokenStorageException;
use JOOservices\Flickr\Tests\Fakes\FakeTransport;
use JOOservices\Flickr\Tests\TestCase;

final class OAuthTest extends TestCase
{
    public function test_signer_builds_deterministic_base_string_and_signature(): void
    {
        $config = new FlickrConfig('key', 'secret');
        $signer = new OAuth1Signer($config);
        $signed = $signer->sign(
            'GET',
            'https://www.flickr.com/services/oauth/request_token',
            ['oauth_callback' => 'http://www.example.com', 'weird' => 'a b&c'],
            nonce: 'nonce',
            timestamp: 123,
        );

        $base = $signer->signatureBaseString('GET', 'https://www.flickr.com/services/oauth/request_token', array_merge([
            'oauth_callback' => 'http://www.example.com',
            'weird' => 'a b&c',
        ], array_diff_key($signed, ['oauth_signature' => true])));

        $expected = base64_encode(hash_hmac('sha1', $base, 'secret&', true));

        $this->assertStringStartsWith('GET&https%3A%2F%2Fwww.flickr.com%2Fservices%2Foauth%2Frequest_token&', $base);
        $this->assertSame($expected, $signed['oauth_signature']);
    }

    public function test_authenticator_parses_tokens_and_builds_authorization_url(): void
    {
        $transport = new FakeTransport;
        $transport->push('oauth_callback_confirmed=true&oauth_token=req&oauth_token_secret=req-secret');
        $transport->push('oauth_token=acc&oauth_token_secret=acc-secret&user_nsid=1&username=viet');
        $auth = new OAuth1Authenticator(new FlickrConfig('key', 'secret', 'https://app.test/callback'), new OAuth1Signer(new FlickrConfig('key', 'secret', 'https://app.test/callback')), $transport);

        $requestToken = $auth->requestToken(AuthPermission::Write);
        $url = $auth->authorizationUrl($requestToken, AuthPermission::Write);
        $accessToken = $auth->accessToken('req', 'verifier');
        $accessQuery = $transport->requests[1]['options']['query'];
        $expectedAccessSignature = (new OAuth1Signer(new FlickrConfig('key', 'secret')))->sign(
            'GET',
            'https://www.flickr.com/services/oauth/access_token',
            ['oauth_verifier' => 'verifier'],
            'req',
            'req-secret',
            $accessQuery['oauth_nonce'],
            (int) $accessQuery['oauth_timestamp'],
        )['oauth_signature'];

        $this->assertTrue($requestToken->oauthCallbackConfirmed);
        $this->assertStringContainsString('oauth_token=req', $url);
        $this->assertStringContainsString('perms=write', $url);
        $this->assertSame($expectedAccessSignature, $accessQuery['oauth_signature']);
        $this->assertSame('acc', $accessToken->oauthToken);
        $this->assertSame('viet', $accessToken->username);
    }

    public function test_authenticator_rejects_missing_verifier_and_token_store_round_trip(): void
    {
        $store = new InMemoryTokenStore;
        $token = new AccessTokenData('token', 'secret');
        $store->put($token);
        $this->assertSame($token, $store->get());
        $store->forget();
        $this->assertNull($store->get());

        $auth = new OAuth1Authenticator(new FlickrConfig('key', 'secret'), new OAuth1Signer(new FlickrConfig('key', 'secret')), new FakeTransport);
        $this->expectException(AuthenticationException::class);
        $auth->accessToken('', '');
    }

    public function test_file_token_store_rejects_corrupted_json(): void
    {
        $path = sys_get_temp_dir().'/flickr-token-'.bin2hex(random_bytes(4)).'.json';
        file_put_contents($path, '{bad');

        $this->expectException(TokenStorageException::class);

        try {
            (new FileTokenStore($path))->get();
        } finally {
            @unlink($path);
        }
    }

    public function test_file_token_store_round_trips_and_forgets_tokens(): void
    {
        $path = sys_get_temp_dir().'/flickr-token-round-trip-'.bin2hex(random_bytes(4)).'.json';
        $store = new FileTokenStore($path);
        $token = new AccessTokenData('token', 'secret', 'user', 'username');

        try {
            $store->put($token);

            $this->assertFileExists($path);
            $this->assertEquals($token, $store->get());

            $store->forget();
            $this->assertFileDoesNotExist($path);
            $store->forget();
            $this->assertFileDoesNotExist($path);
        } finally {
            @unlink($path);
        }
    }

    public function test_file_token_store_rejects_non_object_json(): void
    {
        $path = sys_get_temp_dir().'/flickr-token-scalar-'.bin2hex(random_bytes(4)).'.json';
        file_put_contents($path, '"token"');

        $this->expectException(TokenStorageException::class);

        try {
            (new FileTokenStore($path))->get();
        } finally {
            @unlink($path);
        }
    }

    public function test_null_token_store_discards_tokens(): void
    {
        $store = new NullTokenStore;

        $this->assertNull($store->get());
        $store->put(new AccessTokenData('token', 'secret'));
        $store->forget();
        $this->assertNull($store->get());
    }

    public function test_file_token_store_handles_missing_and_empty_files_without_leaking_secrets(): void
    {
        $missing = sys_get_temp_dir().'/flickr-token-missing-'.bin2hex(random_bytes(4)).'.json';
        $empty = sys_get_temp_dir().'/flickr-token-empty-'.bin2hex(random_bytes(4)).'.json';

        $this->assertNull((new FileTokenStore($missing))->get());

        file_put_contents($empty, '');

        try {
            (new FileTokenStore($empty))->get();
            $this->fail('Empty token file should fail.');
        } catch (TokenStorageException $exception) {
            $this->assertStringNotContainsString('secret', $exception->getMessage());
        } finally {
            @unlink($empty);
        }
    }
}
