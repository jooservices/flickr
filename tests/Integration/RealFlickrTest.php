<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Integration;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Tests\TestCase;

final class RealFlickrTest extends TestCase
{
    protected function setUp(): void
    {
        if (getenv('FLICKR_REAL_TESTS') !== 'true') {
            $this->markTestSkipped('Real Flickr tests are opt-in with FLICKR_REAL_TESTS=true.');
        }
    }

    public function test_public_search_can_reach_flickr_when_explicitly_enabled(): void
    {
        $apiKey = getenv('FLICKR_API_KEY') ?: '';
        $apiSecret = getenv('FLICKR_API_SECRET') ?: '';

        if ($apiKey === '' || $apiSecret === '') {
            $this->markTestSkipped('FLICKR_API_KEY and FLICKR_API_SECRET are required.');
        }

        $flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
        $response = $flickr->photos()->search(new SearchPhotosData(text: 'sunset', perPage: 1));

        $this->assertTrue($response->ok);
    }

    public function test_authenticated_upload_status_when_explicitly_enabled(): void
    {
        $apiKey = getenv('FLICKR_API_KEY') ?: '';
        $apiSecret = getenv('FLICKR_API_SECRET') ?: '';
        $token = getenv('FLICKR_ACCESS_TOKEN') ?: '';
        $tokenSecret = getenv('FLICKR_ACCESS_TOKEN_SECRET') ?: '';

        if ($apiKey === '' || $apiSecret === '' || $token === '' || $tokenSecret === '') {
            $this->markTestSkipped('Flickr API credentials and access token are required.');
        }

        $flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
        $flickr->tokens()->put(new AccessTokenData($token, $tokenSecret));

        $this->assertTrue($flickr->people()->getUploadStatus()->ok);
    }
}
