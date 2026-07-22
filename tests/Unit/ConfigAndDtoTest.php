<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Favorites\FavoritePhotoData;
use JOOservices\Flickr\DTO\Galleries\GalleryData;
use JOOservices\Flickr\DTO\Galleries\GalleryPhotoData;
use JOOservices\Flickr\DTO\Groups\GroupData;
use JOOservices\Flickr\DTO\Groups\GroupPoolData;
use JOOservices\Flickr\DTO\People\PersonData;
use JOOservices\Flickr\DTO\People\UploadStatusData;
use JOOservices\Flickr\DTO\Photos\PhotoData;
use JOOservices\Flickr\DTO\Photos\PhotoExifData;
use JOOservices\Flickr\DTO\Photos\PhotoInfoData;
use JOOservices\Flickr\DTO\Photos\PhotoMetadataData;
use JOOservices\Flickr\DTO\Photos\PhotoPermissionData;
use JOOservices\Flickr\DTO\Photos\PhotoSizeData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\DTO\Photosets\CreatePhotosetData;
use JOOservices\Flickr\DTO\Photosets\PhotosetData;
use JOOservices\Flickr\DTO\Photosets\PhotosetPhotoData;
use JOOservices\Flickr\DTO\Places\PlaceData;
use JOOservices\Flickr\DTO\Tags\MachineTagData;
use JOOservices\Flickr\DTO\Tags\TagData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadTicketData;
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

    public function test_public_dto_shapes_are_constructible(): void
    {
        $dtos = [
            new PaginationOptionsData(startPage: 2, perPage: 10, maxPages: 3, stopWhenEmpty: false),
            new FavoritePhotoData('1', 'owner'),
            new GalleryData('gallery-id', 'Gallery'),
            new GalleryPhotoData('1', 'Title'),
            new GroupData('group-id', 'Group'),
            new GroupPoolData(['id' => '1', 'title' => 'Title']),
            new PersonData('nsid', ['username' => 'username']),
            new UploadStatusData(['is_pro' => true, 'used' => 10, 'max' => 20]),
            new PhotoData('1', 'Title', 'owner'),
            new PhotoExifData(['make' => 'Make', 'model' => 'Model']),
            new PhotoInfoData('1', ['title' => 'Title']),
            new PhotoMetadataData(title: 'Title'),
            new PhotoPermissionData(isPublic: true, isFriend: false, isFamily: false),
            new PhotoSizeData('Large', 'https://example.test/photo.jpg', 640, 480),
            new CreatePhotosetData('Set', '1'),
            new PhotosetData('set-id', 'Title'),
            new PhotosetPhotoData('1', 'Title'),
            new PlaceData('woe:1', 'San Francisco'),
            new MachineTagData('namespace', 'predicate', 'value'),
            new TagData('tag'),
            new UploadTicketData('ticket-id', complete: 0),
        ];

        $this->assertCount(21, $dtos);
    }

    public function test_flickr_config_validations(): void
    {
        try {
            FlickrConfig::from([
                'apiKey' => 'key',
                'apiSecret' => 'secret',
                'timeoutSeconds' => 0,
            ]);
            $this->fail('Expected ConfigurationException for timeoutSeconds < 1');
        } catch (ConfigurationException) {
            $this->addToAssertionCount(1);
        }

        try {
            FlickrConfig::from([
                'apiKey' => 'key',
                'apiSecret' => 'secret',
                'retryTimes' => -1,
            ]);
            $this->fail('Expected ConfigurationException for retryTimes < 0');
        } catch (ConfigurationException) {
            $this->addToAssertionCount(1);
        }

        try {
            FlickrConfig::from([
                'apiKey' => 'key',
                'apiSecret' => 'secret',
                'publicCacheTtlSeconds' => 0,
            ]);
            $this->fail('Expected ConfigurationException for publicCacheTtlSeconds < 1');
        } catch (ConfigurationException) {
            $this->addToAssertionCount(1);
        }

        try {
            FlickrConfig::from([
                'apiKey' => 'key',
                'apiSecret' => 'secret',
                'rateLimitMaxTokens' => 0,
            ]);
            $this->fail('Expected ConfigurationException for rateLimitMaxTokens < 1');
        } catch (ConfigurationException) {
            $this->addToAssertionCount(1);
        }

        try {
            FlickrConfig::from([
                'apiKey' => 'key',
                'apiSecret' => 'secret',
                'rateLimitRefillPerSecond' => 0,
            ]);
            $this->fail('Expected ConfigurationException for rateLimitRefillPerSecond < 1');
        } catch (ConfigurationException) {
            $this->addToAssertionCount(1);
        }
    }

    public function test_pagination_options_data_validations(): void
    {
        try {
            new PaginationOptionsData(maxPages: 0);
            $this->fail('Expected InvalidArgumentException for maxPages < 1');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        try {
            new PaginationOptionsData(perPage: 0);
            $this->fail('Expected InvalidArgumentException for perPage < 1');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }

        try {
            new PaginationOptionsData(startPage: 0);
            $this->fail('Expected InvalidArgumentException for startPage < 1');
        } catch (\InvalidArgumentException) {
            $this->addToAssertionCount(1);
        }
    }
}
