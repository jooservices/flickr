<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Auth\OAuth1Signer;
use JOOservices\Flickr\Client\FlickrClient;
use JOOservices\Flickr\Client\FlickrResponseParser;
use JOOservices\Flickr\Client\FlickrUploadClient;
use JOOservices\Flickr\Client\JooClientTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\CachePolicy;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JOOservices\Flickr\Exceptions\RateLimitException;
use JOOservices\Flickr\Exceptions\TransportException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Tests\Fakes\ArrayCache;
use JOOservices\Flickr\Tests\Fakes\FakeHttpClient;
use JOOservices\Flickr\Tests\Fakes\FakeResponseWrapper;
use JOOservices\Flickr\Tests\Fakes\FakeTransport;
use JOOservices\Flickr\Tests\Fakes\SpySigner;
use JOOservices\Flickr\Tests\Fakes\SpyTokenStore;
use JOOservices\Flickr\Tests\TestCase;
use RuntimeException;

final class ClientAndParserTest extends TestCase
{
    public function test_raw_client_builds_public_json_request_and_maps_success(): void
    {
        $transport = new FakeTransport([new RawResponseData(200, '{"stat":"ok","photos":{"page":1,"pages":2,"perpage":10,"total":"15"}}')]);
        $client = $this->client($transport);

        $response = $client->call(
            'flickr.photos.search',
            ['text' => 'cats', 'per_page' => 10],
            new RequestOptionsData(timeoutSeconds: 3),
        );
        $request = $transport->lastRequest();

        $this->assertTrue($response->ok);
        $this->assertSame(2, $response->pagination?->pages);
        $this->assertSame('GET', $request['method']);
        $this->assertSame('flickr.photos.search', $request['options']['query']['method']);
        $this->assertSame('key', $request['options']['query']['api_key']);
        $this->assertSame(1, $request['options']['query']['nojsoncallback']);
        $this->assertSame(3, $request['options']['timeout']);
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

    public function test_cache_policy_enabled_allows_non_registry_cacheable_get(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"ok"}'),
            new RawResponseData(200, '{"stat":"ok"}'),
        ]);
        $cache = new ArrayCache;
        $client = $this->client($transport, cache: $cache);

        $first = $client->call('flickr.photos.upload.checkTickets', ['tickets' => '1'], new RequestOptionsData(cache: CachePolicy::Enabled));
        $second = $client->call('flickr.photos.upload.checkTickets', ['tickets' => '1'], new RequestOptionsData(cache: CachePolicy::Enabled));

        $this->assertSame($first, $second);
        $this->assertCount(1, $transport->requests);
        $this->assertSame(1, $cache->puts);
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

    public function test_parser_rejects_empty_scalar_and_missing_stat_api_responses(): void
    {
        $parser = new FlickrResponseParser;

        foreach (['', 'true', '{"photos":[]}'] as $body) {
            try {
                $parser->parseApi(new RawResponseData(200, $body));
                $this->fail("Expected invalid response for body [{$body}].");
            } catch (InvalidResponseException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_parser_maps_xml_api_success_failure_and_invalid_xml(): void
    {
        $parser = new FlickrResponseParser;

        $success = $parser->parseApi(new RawResponseData(200, '<rsp stat="ok"><photos page="1"/></rsp>'));
        $failure = $parser->parseApi(new RawResponseData(200, '<rsp stat="fail"><err code="100" msg="Invalid"/></rsp>'));

        $this->assertTrue($success->ok);
        $this->assertSame(['@attributes' => ['stat' => 'ok'], 'photos' => ['@attributes' => ['page' => '1']]], $success->data);
        $this->assertFalse($failure->ok);
        $this->assertSame(100, $failure->error?->code);
        $this->assertSame('Invalid', $failure->error?->message);
        $this->assertArrayHasKey('err', $failure->data);

        foreach (['<rsp>', '<unknown />'] as $body) {
            $previous = libxml_use_internal_errors(true);
            try {
                $parser->parseApi(new RawResponseData(200, $body));
                $this->fail("Expected invalid XML API response for body [{$body}].");
            } catch (InvalidResponseException) {
                $this->addToAssertionCount(1);
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previous);
            }
        }
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

    public function test_parser_maps_root_upload_responses_and_rejects_upload_errors(): void
    {
        $parser = new FlickrResponseParser;

        $photo = $parser->parseUpload(new RawResponseData(200, '<photoid secret="s" originalsecret="o">123</photoid>'));
        $ticket = $parser->parseUpload(new RawResponseData(200, '<ticketid>999</ticketid>'));

        $this->assertSame('123', $photo->photoId);
        $this->assertSame('s', $photo->secret);
        $this->assertSame('o', $photo->originalSecret);
        $this->assertSame('999', $ticket->ticketId);

        foreach (['', '<rsp stat="fail"><err code="2" msg="Nope"/></rsp>', '<rsp stat="ok"/>', '<bad'] as $body) {
            $previous = libxml_use_internal_errors(true);
            try {
                $parser->parseUpload(new RawResponseData(200, $body));
                $this->fail("Expected invalid upload response for body [{$body}].");
            } catch (InvalidResponseException) {
                $this->addToAssertionCount(1);
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previous);
            }
        }
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

    public function test_upload_client_quotes_multi_word_tags(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'flickr-upload-tags-');
        file_put_contents($path, 'image-bytes');
        $transport = new FakeTransport([new RawResponseData(200, '<rsp stat="ok"><photoid>1</photoid></rsp>')]);
        $client = new FlickrUploadClient(
            new FlickrConfig('key', 'secret'),
            $transport,
            new OAuth1Signer(new FlickrConfig('key', 'secret')),
            new InMemoryTokenStore(new AccessTokenData('token', 'token-secret')),
        );

        try {
            $client->upload(UploadPhotoData::from([
                'path' => $path,
                'tags' => ['san francisco', 'php'],
            ]));
        } finally {
            @unlink($path);
        }

        $multipart = $transport->lastRequest()['options']['multipart'];
        $tagsPart = null;

        foreach ($multipart as $part) {
            if ($part['name'] === 'tags') {
                $tagsPart = $part['contents'];
            }
        }

        $this->assertSame('"san francisco" php', $tagsPart);
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

    public function test_joo_client_transport_maps_psr_responses_and_wraps_failures(): void
    {
        $fail = false;
        $client = new FakeHttpClient(function () use (&$fail): FakeResponseWrapper {
            if ($fail) {
                throw new RuntimeException('Network failed.');
            }

            return new FakeResponseWrapper(201, 'body', ['X-Test' => ['yes']]);
        });
        $transport = new JooClientTransport($client);

        $response = $transport->request('GET', 'https://example.test', ['query' => ['a' => 'b']]);

        $this->assertSame(201, $response->statusCode);
        $this->assertSame('body', $response->body);
        $this->assertSame(['yes'], $response->headers['X-Test']);

        $fail = true;
        $this->expectException(TransportException::class);
        $transport->request('GET', 'https://example.test');
    }

    public function test_transport_exception_redacts_sensitive_query_parameters(): void
    {
        $client = new FakeHttpClient(function (): never {
            throw new RuntimeException(
                'GET https://api.flickr.com/services/rest?api_key=secret-key&oauth_token=token-value&oauth_signature=sig-value failed'
            );
        });

        $transport = new JooClientTransport($client);

        try {
            $transport->request('GET', 'https://api.flickr.com/services/rest');
            $this->fail('Expected TransportException.');
        } catch (TransportException $exception) {
            $message = $exception->getMessage();
            $this->assertStringNotContainsString('secret-key', $message);
            $this->assertStringNotContainsString('token-value', $message);
            $this->assertStringNotContainsString('sig-value', $message);
            $this->assertStringContainsString('[redacted]', $message);
        }
    }

    public function test_client_throws_rate_limit_exception_on_http_429(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(429, 'Rate Limit Exceeded', ['Retry-After' => ['60']]),
        ]);
        $client = $this->client($transport);

        try {
            $client->call('flickr.photos.search');
            $this->fail('Expected RateLimitException to be thrown.');
        } catch (RateLimitException $exception) {
            $this->assertSame('Flickr rate limit exceeded.', $exception->getMessage());
            $this->assertSame(60, $exception->retryAfter());
            $this->assertSame(429, $exception->httpStatus());
            $this->assertTrue($exception->retryable());
        }
    }

    public function test_client_reads_retry_after_header_case_insensitively(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(429, 'Rate Limit Exceeded', ['retry-after' => ['30']]),
        ]);
        $client = $this->client($transport);

        try {
            $client->call('flickr.photos.search');
            $this->fail('Expected RateLimitException.');
        } catch (RateLimitException $exception) {
            $this->assertSame(30, $exception->retryAfter());
        }
    }

    public function test_parser_includes_http_status_in_malformed_json_message(): void
    {
        $parser = new FlickrResponseParser;

        try {
            $parser->parseApi(new RawResponseData(500, '{not-json'));
            $this->fail('Expected InvalidResponseException.');
        } catch (InvalidResponseException $exception) {
            $this->assertStringContainsString('HTTP 500', $exception->getMessage());
        }
    }

    public function test_client_throws_authorization_exception_on_auth_errors(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"fail","code":99,"message":"Insufficient permissions"}'),
        ]);
        $client = $this->client($transport);

        try {
            $client->call('flickr.photos.search', [], new RequestOptionsData(throwOnApiError: true));
            $this->fail('Expected AuthorizationException to be thrown.');
        } catch (AuthorizationException $exception) {
            $this->assertSame('Insufficient permissions', $exception->getMessage());
            $this->assertSame(99, $exception->apiCode());
            $this->assertSame(200, $exception->httpStatus());
            $this->assertFalse($exception->retryable());
        }
    }

    public function test_client_throws_retryable_api_exception_for_service_unavailable(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"fail","code":105,"message":"Service currently unavailable"}'),
        ]);
        $client = $this->client($transport);

        try {
            $client->call('flickr.photos.search', [], new RequestOptionsData(throwOnApiError: true));
            $this->fail('Expected ApiException to be thrown.');
        } catch (ApiException $exception) {
            $this->assertSame(105, $exception->apiCode());
            $this->assertTrue($exception->retryable());
            $this->assertSame(200, $exception->httpStatus());
        }
    }

    public function test_client_throws_api_exception_on_other_api_errors(): void
    {
        $transport = new FakeTransport([
            new RawResponseData(200, '{"stat":"fail","code":1,"message":"Photo not found"}'),
        ]);
        $client = $this->client($transport);

        try {
            $client->call('flickr.photos.search', [], new RequestOptionsData(throwOnApiError: true));
            $this->fail('Expected ApiException to be thrown.');
        } catch (ApiException $exception) {
            $this->assertSame('Photo not found', $exception->getMessage());
            $this->assertSame(1, $exception->apiCode());
            $this->assertFalse($exception->retryable());
            $this->assertSame(200, $exception->httpStatus());
        }
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
