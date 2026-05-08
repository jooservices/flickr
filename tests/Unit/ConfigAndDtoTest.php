<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\ContentType;
use JOOservices\Flickr\Enums\HiddenStatus;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Enums\SafetyLevel;
use JOOservices\Flickr\Exceptions\ConfigurationException;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Tests\TestCase;

final class ConfigAndDtoTest extends TestCase
{
    public function test_config_requires_api_key_and_secret_and_has_official_defaults(): void
    {
        $config = FlickrConfig::from(['apiKey' => 'key', 'apiSecret' => 'secret']);

        $this->assertSame('https://www.flickr.com/services/rest', $config->restEndpoint);
        $this->assertSame('https://up.flickr.com/services/upload', $config->uploadEndpoint);
        $this->assertSame('https://up.flickr.com/services/replace', $config->replaceEndpoint);

        $this->expectException(ConfigurationException::class);
        FlickrConfig::from(['apiKey' => '', 'apiSecret' => 'secret']);
    }

    public function test_search_and_upload_defaults(): void
    {
        $search = SearchPhotosData::from(['text' => 'sunset']);
        $upload = UploadPhotoData::from(['path' => '/tmp/photo.jpg']);

        $this->assertSame(1, $search->page);
        $this->assertSame(100, $search->perPage);
        $this->assertSame(Privacy::Private, $upload->privacy);
        $this->assertSame(SafetyLevel::Safe, $upload->safetyLevel);
        $this->assertSame(ContentType::Photo, $upload->contentType);
        $this->assertSame(HiddenStatus::Visible, $upload->hidden);
        $this->assertFalse($upload->async);
    }

    public function test_replace_requires_photo_id_and_privacy_maps_to_flickr_upload_fields(): void
    {
        $this->assertSame(['is_public' => 0, 'is_friend' => 1, 'is_family' => 1], Privacy::FriendsAndFamily->uploadFields());
        $this->assertSame(1, SafetyLevel::Safe->value);
        $this->assertSame(1, ContentType::Photo->value);
        $this->assertSame(1, HiddenStatus::Visible->value);
        $this->assertSame('GET', HttpMethod::Get->value);

        $this->expectException(\InvalidArgumentException::class);
        ReplacePhotoData::from(['path' => '/tmp/photo.jpg', 'photoId' => '']);
    }

    public function test_package_autoloads_factory_with_fake_transport_and_has_no_laravel_surface(): void
    {
        $flickr = FlickrFactory::make(new FlickrConfig('key', 'secret'), transport: FakeFlickrTransport::new());

        $this->assertTrue($flickr->raw()->call('flickr.test.echo')->ok);
        $this->assertFalse(class_exists('JOOservices\\Flickr\\FlickrServiceProvider'));
        $this->assertFalse(class_exists('JOOservices\\Flickr\\Facades\\Flickr'));
    }
}
