<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Testing\FlickrFake;
use JOOservices\Flickr\Tests\TestCase;
use RuntimeException;

final class FlickrFakeAndDescribeTest extends TestCase
{
    public function test_describe_returns_public_method_info(): void
    {
        $flickr = FlickrFactory::make(
            new FlickrConfig('key', 'secret', enableCircuitBreaker: false, enableRateLimit: false),
            transport: FakeFlickrTransport::new(),
        );

        $info = $flickr->describe('flickr.photos.search');

        $this->assertNotNull($info);
        $this->assertSame('flickr.photos.search', $info->name);
        $this->assertSame(HttpMethod::Get, $info->httpMethod);
        $this->assertTrue((new \ReflectionProperty($info, 'name'))->isReadOnly());
        $this->assertNull($flickr->describe('flickr.photo.search'));
    }

    public function test_typo_suggestion_appended_on_api_failure(): void
    {
        $transport = FakeFlickrTransport::new()->pushJson([
            'stat' => 'fail',
            'code' => 112,
            'message' => 'Method not found',
        ]);
        $flickr = FlickrFactory::make(
            new FlickrConfig('key', 'secret', enableCircuitBreaker: false, enableRateLimit: false),
            transport: $transport,
        );

        try {
            $flickr->raw()->call('flickr.photo.search', [], new RequestOptionsData(throwOnApiError: true));
            $this->fail('Expected ApiException');
        } catch (ApiException $exception) {
            $this->assertStringContainsString('Did you mean [flickr.photos.search]?', $exception->getMessage());
        }
    }

    public function test_registry_suggestion_for_close_typo(): void
    {
        $registry = FlickrMethodRegistry::default();

        $this->assertSame('flickr.photos.search', $registry->suggestionFor('flickr.photo.search'));
        $this->assertNull($registry->suggestionFor('flickr.photos.search'));
    }

    public function test_flickr_fake_responds_and_asserts_calls(): void
    {
        $fake = FlickrFake::create();
        $fake->respond('flickr.photos.search', [
            'photos' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 1,
                'total' => 0,
                'photo' => [],
            ],
        ]);

        $response = $fake->flickr()->photos()->search(SearchPhotosData::from([
            'text' => 'cat',
            'perPage' => 1,
        ]));

        $this->assertTrue($response->ok);
        $fake->assertCalled('flickr.photos.search');
        $fake->assertCalled('flickr.photos.search', ['text' => 'cat']);
        $this->assertNotEmpty($fake->calls('flickr.photos.search'));

        try {
            $fake->assertCalled('flickr.photos.search', ['text' => 'dog']);
            $this->fail('Expected RuntimeException for mismatched parameters');
        } catch (RuntimeException) {
            $this->addToAssertionCount(1);
        }
    }

    public function test_flickr_fake_respond_error_and_missing_assert(): void
    {
        $fake = FlickrFake::create();
        $fake->respondError('flickr.photos.search', 1, 'Photo not found');

        $response = $fake->flickr()->raw()->call('flickr.photos.search');
        $this->assertFalse($response->ok);

        $this->expectException(RuntimeException::class);
        $fake->assertCalled('flickr.people.getInfo');
    }

    public function test_flickr_fake_rejects_a_response_queued_for_a_different_method(): void
    {
        $fake = FlickrFake::create();
        $fake->respond('flickr.people.getInfo');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Expected Flickr method [flickr.people.getInfo], received [flickr.photos.search].');

        $fake->flickr()->raw()->call('flickr.photos.search');
    }
}
