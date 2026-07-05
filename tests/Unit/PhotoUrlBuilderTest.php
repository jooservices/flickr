<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\DTO\Photos\PhotoInfoData;
use JOOservices\Flickr\Enums\PhotoSize;
use JOOservices\Flickr\Support\PhotoUrlBuilder;
use JOOservices\Flickr\Tests\TestCase;

final class PhotoUrlBuilderTest extends TestCase
{
    public function test_builds_size_url_from_photo_info(): void
    {
        $builder = new PhotoUrlBuilder;
        $photo = new PhotoInfoData('123', ['secret' => 'abc', 'server' => '65535']);

        $this->assertSame(
            'https://live.staticflickr.com/65535/123_abc.jpg',
            $builder->sizeUrl($photo, PhotoSize::Medium),
        );
        $this->assertSame(
            'https://live.staticflickr.com/65535/123_abcb.jpg',
            $builder->sizeUrl($photo, PhotoSize::Large),
        );
    }

    public function test_returns_empty_string_when_photo_metadata_is_incomplete(): void
    {
        $builder = new PhotoUrlBuilder;

        $this->assertSame('', $builder->sizeUrl(new PhotoInfoData('123'), PhotoSize::Medium));
    }
}
