<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Tests\TestCase;
use RuntimeException;

final class FakeFlickrTransportTest extends TestCase
{
    public function test_it_queues_json_responses_and_inspects_rest_requests(): void
    {
        $transport = FakeFlickrTransport::new()->pushJson([
            'stat' => 'ok',
            'photos' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 1,
                'total' => 0,
                'photo' => [],
            ],
        ]);
        $flickr = FlickrFactory::make(new FlickrConfig('key', 'secret'), transport: $transport);

        $response = $flickr->photos()->search(SearchPhotosData::from([
            'text' => 'cat',
            'perPage' => 1,
        ]));

        $this->assertTrue($response->ok);
        $transport->assertSentMethod('flickr.photos.search');
        $this->assertSame('cat', $transport->lastRequest()['options']['query']['text']);
    }

    public function test_it_records_multipart_upload_requests(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'flickr-fake-upload-');
        file_put_contents($path, 'image-bytes');

        $transport = FakeFlickrTransport::new()->pushUploadTicket('123');
        $flickr = FlickrFactory::make(
            new FlickrConfig('key', 'secret'),
            tokenStore: new InMemoryTokenStore(new AccessTokenData('token', 'token-secret')),
            transport: $transport,
        );

        try {
            $result = $flickr->uploads()->upload(UploadPhotoData::from([
                'path' => $path,
                'privacy' => Privacy::Private,
                'async' => true,
            ]));
        } finally {
            @unlink($path);
        }

        $this->assertSame('123', $result->ticketId);
        $transport->assertLastRequestIsMultipart();
    }

    public function test_assertions_fail_clearly(): void
    {
        $this->expectException(RuntimeException::class);

        FakeFlickrTransport::new()->assertSentMethod('flickr.photos.search');
    }
}
