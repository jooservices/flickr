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
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Tests\Fakes\FakeTransport;
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

        $this->assertSame('999', $result->ticketId);
        $this->assertContains('photo', $names);
        $this->assertContains('oauth_signature', $names);
        $this->assertContains('is_friend', $names);
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

    private function client(FakeTransport $transport, ?InMemoryTokenStore $tokens = null): FlickrClient
    {
        $config = new FlickrConfig('key', 'secret');

        return new FlickrClient(
            $config,
            $transport,
            new OAuth1Signer($config),
            $tokens ?? new InMemoryTokenStore,
            FlickrMethodRegistry::default(),
        );
    }
}
