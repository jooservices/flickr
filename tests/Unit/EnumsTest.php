<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Enums\PhotoExtra;
use JOOservices\Flickr\Enums\Privacy;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    /**
     * @return iterable<string, array{0: Privacy, 1: array{is_public: int, is_friend: int, is_family: int}}>
     */
    public static function providePrivacyFields(): iterable
    {
        yield 'public' => [Privacy::Public, ['is_public' => 1, 'is_friend' => 0, 'is_family' => 0]];
        yield 'private' => [Privacy::Private, ['is_public' => 0, 'is_friend' => 0, 'is_family' => 0]];
        yield 'friends' => [Privacy::Friends, ['is_public' => 0, 'is_friend' => 1, 'is_family' => 0]];
        yield 'family' => [Privacy::Family, ['is_public' => 0, 'is_friend' => 0, 'is_family' => 1]];
        yield 'friends and family' => [Privacy::FriendsAndFamily, ['is_public' => 0, 'is_friend' => 1, 'is_family' => 1]];
    }

    /**
     * @param array{is_public: int, is_friend: int, is_family: int} $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providePrivacyFields')]
    public function testPrivacyUploadFields(Privacy $privacy, array $expected): void
    {
        self::assertSame($expected, $privacy->uploadFields());
    }

    public function testPhotoExtraJoinsValues(): void
    {
        self::assertSame(
            'description,tags,url_o',
            PhotoExtra::join([PhotoExtra::Description, PhotoExtra::Tags, PhotoExtra::UrlOriginal]),
        );
        self::assertSame('', PhotoExtra::join([]));
    }

    public function testHttpMethodRestVerbs(): void
    {
        self::assertSame(['GET', 'POST'], HttpMethod::restVerbs());
    }
}
