<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Dtos\Photos\PhotoData;
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
            new \JOOservices\Flickr\Dtos\Photos\SearchPhotosData(perPage: $pageOrPer);
        } else {
            new \JOOservices\Flickr\Dtos\Photos\SearchPhotosData(page: -1);
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

        $result = $this->photos()->search(new \JOOservices\Flickr\Dtos\Photos\SearchPhotosData(text: 'sunset', perPage: 100, page: 2));

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
            'https://farm6.static.flickr.com/sv/1_sec_m.jpg',
            PhotoUrlBuilder::build($complete, 'm'),
        );
        self::assertSame(
            'https://farm6.static.flickr.com/sv/1_sec_m.jpg',
            PhotoUrlBuilder::build($complete, \JOOservices\Flickr\Enums\PhotoSize::Small),
        );
        self::assertSame(
            'https://farm6.static.flickr.com/sv/1_sec.jpg',
            PhotoUrlBuilder::build($complete, \JOOservices\Flickr\Enums\PhotoSize::Medium),
        );
        self::assertNull(PhotoUrlBuilder::build(new PhotoData(id: '1')));
    }
}
