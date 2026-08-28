<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Auth\AccessTokenData;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Dtos\Photos\PhotoData;
use JOOservices\Flickr\Dtos\Photos\SearchPhotosData;
use JOOservices\Flickr\Enums\AuthenticationMode;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Support\PhotoUrlBuilder;
use JOOservices\Flickr\Tests\Support\FakeTransport;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use JOOservices\Flickr\Services\PhotosApi;
use PHPUnit\Framework\TestCase;

final class TypedWorkflowTest extends TestCase
{
    private FakeTransport $transport;

    protected function setUp(): void
    {
        $this->transport = new FakeTransport();
    }

    private function photos(): PhotosApi
    {
        return new PhotosApi(PipelineFactory::api($this->transport));
    }

    /**
     * @return iterable<string, array{0: int}>
     */
    public static function provideInvalidSearchInputs(): iterable
    {
        yield 'perPage 0' => [0];
        yield 'perPage 501' => [501];
        yield 'page 0' => [-1];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideInvalidSearchInputs')]
    public function testSearchInputValidation(int $pageOrPer): void
    {
        $this->expectException(InvalidArgumentException::class);

        if ($pageOrPer < 1 || $pageOrPer > 500) {
            new SearchPhotosData(perPage: $pageOrPer);
        } else {
            new SearchPhotosData(page: -1);
        }
    }

    public function testSearchHydratesTypedResults(): void
    {
        $this->transport->queue(new RawResponseData(200, [], (string) json_encode([
            'stat' => 'ok',
            'photos' => [
                'page' => 2,
                'pages' => '10',
                'perpage' => '100',
                'total' => '943',
                'photo' => [
                    ['id' => '11', 'owner' => 'o', 'title' => 'T', 'secret' => 's', 'server' => 'sv', 'farm' => '6'],
                    ['no-id-here' => true],
                ],
            ],
        ])));

        $result = $this->photos()->search(new SearchPhotosData(text: 'sunset', perPage: 100, page: 2));

        self::assertCount(1, $result->photos);
        self::assertSame('11', $result->photos[0]->id);
        self::assertSame(2, $result->page);
        self::assertSame(10, $result->pages);
        self::assertSame(943, $result->total);

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);
        self::assertSame('flickr.photos.search', $dispatched['method']);
        self::assertSame('100', $dispatched['per_page']);
        self::assertSame('2', $dispatched['page']);
    }

    public function testGetSizesReturnsTypedList(): void
    {
        $this->transport->queue(new RawResponseData(200, [], (string) json_encode([
            'stat' => 'ok',
            'sizes' => [
                'size' => [
                    ['label' => 'Square', 'source' => 'https://x/s.jpg', 'width' => '75', 'height' => 75],
                    ['broken' => true],
                    ['label' => 'Large', 'source' => 'https://x/l.jpg'],
                ],
            ],
        ])));

        $sizes = $this->photos()->getSizes('55');

        self::assertCount(2, $sizes);
        self::assertSame(75, $sizes[0]->width);
        self::assertNull($sizes[1]->width);
    }

    public function testGetInfoAndExifHydrate(): void
    {
        $this->transport->queue(
            new RawResponseData(200, [], (string) json_encode([
                'stat' => 'ok',
                'photo' => [
                    'id' => '77',
                    'title' => ['_content' => 'Title'],
                    'description' => ['_content' => 'Desc'],
                    'owner' => ['nsid' => '123', 'username' => 'vu'],
                    'dates' => [
                        'posted' => '1234567890',
                        'taken' => '2004-11-05 22:32:18',
                    ],
                    'views' => '42',
                ],
            ])),
            new RawResponseData(200, [], (string) json_encode([
                'stat' => 'ok',
                'photo' => [
                    'id' => '77',
                    'exif' => [
                        ['label' => 'Model', 'raw' => ['_content' => 'X100V'], 'clean' => null],
                    ],
                ],
            ])),
        );

        $info = $this->photos()->getInfo('77');
        self::assertSame('Title', $info->title);
        self::assertSame('vu', $info->ownerUsername);
        self::assertSame('1234567890', $info->datePosted);
        self::assertSame('2004-11-05 22:32:18', $info->dateTaken);
        self::assertSame(42, $info->views);

        $exif = $this->photos()->getExif('77');
        self::assertSame('77', $exif->photoId);
        self::assertSame('Model', $exif->exif[0]->name);
        self::assertSame('X100V', $exif->exif[0]->raw);
    }

    public function testPhotoUrlBuilderNeverBuildsMalformedUrls(): void
    {
        $complete = new PhotoData(id: '1', secret: 'sec', server: 'sv', farm: 6);

        self::assertSame(
            'https://live.staticflickr.com/sv/1_sec_m.jpg',
            PhotoUrlBuilder::build($complete, 'm'),
        );
        self::assertSame(
            'https://live.staticflickr.com/sv/1_sec_m.jpg',
            PhotoUrlBuilder::build($complete, \JOOservices\Flickr\Enums\PhotoSize::Small),
        );
        self::assertSame(
            'https://live.staticflickr.com/sv/1_sec.jpg',
            PhotoUrlBuilder::build($complete, \JOOservices\Flickr\Enums\PhotoSize::Medium),
        );
        self::assertNull(PhotoUrlBuilder::build(new PhotoData(id: '1')));
    }

    public function testGetInfoHydratesLegacyTopLevelDateFields(): void
    {
        $this->transport->queue(new RawResponseData(200, [], (string) json_encode([
            'stat' => 'ok',
            'photo' => [
                'id' => '77',
                'dateposted' => '111',
                'datetaken' => '2001-01-01 00:00:00',
            ],
        ])));

        $info = $this->photos()->getInfo('77');

        self::assertSame('111', $info->datePosted);
        self::assertSame('2001-01-01 00:00:00', $info->dateTaken);
    }

    public function testGetInfoThrowsOnStatFail(): void
    {
        $this->transport->queue(new RawResponseData(
            200,
            [],
            '{"stat":"fail","code":"1","message":"Photo not found"}',
        ));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Photo not found');

        $this->photos()->getInfo('missing');
    }

    public function testSearchSignsWhenAuthenticatedModeIsRequested(): void
    {
        $tokens = new InMemoryTokenStore();
        $tokens->put(new AccessTokenData('tok', 'sec', AuthPermission::Write));
        $this->transport->queue(new RawResponseData(200, [], (string) json_encode([
            'stat' => 'ok',
            'photos' => ['page' => 1, 'pages' => 1, 'perpage' => 30, 'total' => '0', 'photo' => []],
        ])));

        $photos = new PhotosApi(PipelineFactory::apiWithTokens($this->transport, $tokens));
        $photos->search(
            new SearchPhotosData(text: 'mine'),
            new ApiCallOptions(mode: AuthenticationMode::Authenticated),
        );

        $dispatched = PipelineFactory::dispatchedParameters($this->transport->sentRequests()[0][0]);

        self::assertArrayHasKey('oauth_signature', $dispatched);
        self::assertArrayHasKey('oauth_token', $dispatched);
        self::assertSame('tok', $dispatched['oauth_token']);
        self::assertSame('flickr.photos.search', $dispatched['method']);
    }

    public function testSearchDefaultPerPageIsThirty(): void
    {
        self::assertSame(30, (new SearchPhotosData())->perPage);
    }

    public function testSearchHydratesASinglePhotoObject(): void
    {
        $this->transport->queue(new RawResponseData(200, [], (string) json_encode([
            'stat' => 'ok',
            'photos' => [
                'page' => 1,
                'pages' => 1,
                'perpage' => 30,
                'total' => 1,
                'photo' => ['id' => '11', 'owner' => 'o', 'title' => 'T'],
            ],
        ])));

        $result = $this->photos()->search(new SearchPhotosData(text: 'one'));

        self::assertCount(1, $result->photos);
        self::assertSame('11', $result->photos[0]->id);
    }
}
