<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Client\FlickrClient;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Client\FlickrUploadClient;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\CachePolicy;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Tests\Fakes\ArrayCache;
use JOOservices\Flickr\Tests\Fakes\FakeTransport;
use JOOservices\Flickr\Tests\Fakes\SpySigner;
use JOOservices\Flickr\Tests\Fakes\SpyTokenStore;
use JOOservices\Flickr\Tests\TestCase;

final class ClientAndParserTest extends TestCase
{
    public function test_raw_client_builds_public_json_request_and_maps_success(): void
    {
        $transport = new FakeTransport([new RawResponseData(200, '{"stat":"ok","photos":{"page":1,"pages":2,"perpage":10,"total":"15"}}')]);
        $client = $this->client($transport);

        $response = $client->call('flickr.photos.search', ['text' => 'cats', 'per_page' => 10]);
        $request = $transport->lastRequest();

        $this->assertTrue($response->ok);
        $this->assertSame(2, $response->pagination?->pages);
        $this->assertSame('GET', $request['method']);
        $this->assertSame('flickr.photos.search', $request['options']['query']['method']);
        $this->assertSame('key', $request['options']['query']['api_key']);
        $this->assertSame(1, $request['options']['query']['nojsoncallback']);
    }

    public function test_raw_client_allows_unknown_methods_and_requires_token_for_authenticated_calls(): void
    {
        $transport = new FakeTransport([new RawResponseData(200, '{"stat":"ok"}')]);
        $client = $this->client($transport);
        $client->call('flickr.future.method');

        $this->assertSame('flickr.future.method', $transport->lastRequest()['options']['query']['method']);

        $this->expectException(AuthenticationException::class);
        $client->call('flickr.photos.delete', ['photo_id' => '1']);
    }

    public function test_raw_client_signs_authenticated_mutation_and_uses_post(): void
    {
        $transport = new FakeTransport([new RawResponseData(200, '{"stat":"ok"}')]);
        $client = $this->client($transport, new InMemoryTokenStore(new AccessTokenData('token', 'token-secret')));

        $client->call('flickr.photos.delete', ['photo_id' => '1'], new RequestOptionsData(authenticated: true));
        $request = $transport->lastRequest();

        $this->assertSame('POST', $request['method']);
        $this->assertSame('token', $request['options']['form_params']['oauth_token']);
        $this->assertArrayHasKey('oauth_signature', $request['options']['form_params']);
    }

    public function test_public_cacheable_get_uses_cache_and_stable_parameter_keys(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"ok","photos":{"page":1,"pages":1,"perpage":10,"total":"1","photo":[{"id":"1"}]}}'),
        ]);
        $cache = new ArrayCache;
        $client = $this->client($transport, cache: $cache);

        $first = $client->call('flickr.photos.search', ['text' => 'cats', 'per_page' => 10]);
        $second = $client->call('flickr.photos.search', ['per_page' => 10, 'text' => 'cats']);

        $this->assertTrue($first->ok);
        $this->assertSame($first, $second);
        $this->assertCount(1, $transport->requests);
        $this->assertSame(1, $cache->puts);
        $this->assertSame(300, $cache->lastTtl);
    }

    public function test_cache_bypasses_authenticated_auth_required_post_failed_and_disabled_requests(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"ok"}'),
            new RawResponseData(200, '{"stat":"ok"}'),
            new RawResponseData(200, '{"stat":"ok"}'),
            new RawResponseData(200, '{"stat":"fail","code":1,"message":"Nope"}'),
            new RawResponseData(200, '{"stat":"ok"}'),
        ]);
        $cache = new ArrayCache;
        $client = $this->client($transport, new InMemoryTokenStore(new AccessTokenData('token', 'token-secret')), $cache);

        $client->call('flickr.photos.search', [], new RequestOptionsData(authenticated: true));
        $client->call('flickr.photos.getContactsPhotos');
        $client->call('flickr.photos.delete', ['photo_id' => '1']);
        $client->call('flickr.photos.search', ['text' => 'failed']);
        $client->call('flickr.photos.search', ['text' => 'disabled'], new RequestOptionsData(cache: CachePolicy::Disabled));

        $this->assertCount(5, $transport->requests);
        $this->assertSame(0, $cache->puts);
    }

    public function test_request_signing_guardrails_avoid_token_and_signer_work_for_public_gets(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"ok"}'),
            new RawResponseData(200, '{"stat":"ok"}'),
        ]);
        $signer = new SpySigner;
        $tokens = new SpyTokenStore(new AccessTokenData('token', 'token-secret'));
        $config = new FlickrConfig('key', 'secret');
        $client = new FlickrClient($config, $transport, $signer, $tokens, FlickrMethodRegistry::default());

        $client->call('flickr.photos.search');

        $this->assertSame(0, $tokens->getCalls);
        $this->assertSame(0, $signer->signCalls);

        $client->call('flickr.photos.delete', ['photo_id' => '1']);

        $this->assertSame(1, $tokens->getCalls);
        $this->assertSame(1, $signer->signCalls);
    }

    public function test_parser_maps_failure_and_rejects_malformed_responses(): void
    {
        $parser = new FlickrResponseParser;
        $failure = $parser->parseApi(new RawResponseData(200, '{"stat":"fail","code":100,"message":"Invalid API Key"}'));

        $this->assertFalse($failure->ok);
        $this->assertSame(100, $failure->error?->code);

        $this->expectException(InvalidResponseException::class);
        $parser->parseApi(new RawResponseData(200, '{"ok"'));
    }

    public function test_parser_maps_xml_upload_photo_and_ticket_responses(): void
    {
        $parser = new FlickrResponseParser;
        $sync = $parser->parseUpload(new RawResponseData(200, '<rsp stat="ok"><photoid secret="s">123</photoid></rsp>'));
        $async = $parser->parseUpload(new RawResponseData(200, '<rsp stat="ok"><ticketid>999</ticketid></rsp>'));

        $this->assertSame('123', $sync->photoId);
        $this->assertSame('s', $sync->secret);
        $this->assertSame('999', $async->ticketId);
    }

    public function test_upload_client_validates_file_and_builds_signed_multipart_request(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'flickr-upload-');
        file_put_contents($path, 'image-bytes');
        $transport = new FakeTransport([new RawResponseData(200, '<rsp stat="ok"><ticketid>999</ticketid></rsp>')]);
        $client = new FlickrUploadClient(
            new FlickrConfig('key', 'secret'),
            $transport,
            new OAuth1Signer(new FlickrConfig('key', 'secret')),
            new InMemoryTokenStore(new AccessTokenData('token', 'token-secret')),
        );

        try {
            $result = $client->upload(UploadPhotoData::from([
                'path' => $path,
                'tags' => ['php', 'flickr'],
                'privacy' => Privacy::Friends,
                'async' => true,
            ]));
        } finally {
            @unlink($path);
        }

        $multipart = $transport->lastRequest()['options']['multipart'];
        $names = array_column($multipart, 'name');
        $photo = $multipart[array_search('photo', $names, true)];

        $this->assertSame('999', $result->ticketId);
        $this->assertContains('photo', $names);
        $this->assertContains('oauth_signature', $names);
        $this->assertContains('is_friend', $names);
        $this->assertFalse(is_resource($photo['contents']));
    }

    public function test_upload_client_rejects_missing_file_and_missing_token(): void
    {
        $client = new FlickrUploadClient(
            new FlickrConfig('key', 'secret'),
            new FakeTransport,
            new OAuth1Signer(new FlickrConfig('key', 'secret')),
            new InMemoryTokenStore,
        );

        $this->expectException(UploadException::class);
        $client->replace(new ReplacePhotoData('/missing/photo.jpg', '1'));
    }

    private function client(FakeTransport $transport, ?InMemoryTokenStore $tokens = null, ?ArrayCache $cache = null): FlickrClient
    {
        $config = new FlickrConfig('key', 'secret');

        return new FlickrClient(
            $config,
            $transport,
            new OAuth1Signer($config),
            $tokens ?? new InMemoryTokenStore,
            FlickrMethodRegistry::default(),
            cache: $cache ?? new ArrayCache,
        );
    }
}
