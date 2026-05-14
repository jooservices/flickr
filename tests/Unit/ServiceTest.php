<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Contracts\Client\FlickrUploadClientContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\DTO\Photosets\CreatePhotosetData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;
use JOOservices\Flickr\Services\PeopleService;
use JOOservices\Flickr\Services\PhotoService;
use JOOservices\Flickr\Services\PhotosetService;
use JOOservices\Flickr\Services\UploadService;
use JOOservices\Flickr\Tests\Fakes\FakeRawApiService;
use JOOservices\Flickr\Tests\TestCase;

final class ServiceTest extends TestCase
{
    public function test_photo_service_builds_expected_raw_calls(): void
    {
        $raw = new FakeRawApiService;
        $photos = new PhotoService($raw);

        $photos->search(SearchPhotosData::from(['text' => 'sunset', 'tags' => ['landscape'], 'perPage' => 20]));
        $this->assertSame('flickr.photos.search', $raw->lastCall()['method']);
        $this->assertSame(20, $raw->lastCall()['parameters']['per_page']);

        $photos->setTags('123', ['php', 'sdk']);
        $this->assertSame('php sdk', $raw->lastCall()['parameters']['tags']);

        $photos->delete('123');
        $this->assertSame('flickr.photos.delete', $raw->lastCall()['method']);
    }

    public function test_people_photoset_and_upload_ticket_services_build_calls(): void
    {
        $raw = new FakeRawApiService;
        (new PeopleService($raw))->getUploadStatus();
        $this->assertSame('flickr.people.getUploadStatus', $raw->lastCall()['method']);

        $photosets = new PhotosetService($raw);
        $photosets->create(new CreatePhotosetData('Set', '1', 'Desc'));
        $this->assertSame('flickr.photosets.create', $raw->lastCall()['method']);
        $photosets->getPhotos('set-1', ['url_o'], 2, 50);
        $this->assertSame(['url_o'], $raw->lastCall()['parameters']['extras']);

        $upload = new UploadService(new class implements FlickrUploadClientContract
        {
            public function upload(UploadPhotoData $data): UploadResultData
            {
                return new UploadResultData(ticketId: '1');
            }

            public function replace(ReplacePhotoData $data): UploadResultData
            {
                return new UploadResultData(photoId: '1');
            }
        }, $raw);
        $upload->checkTickets([' 1 ', '2']);
        $this->assertSame('flickr.photos.upload.checkTickets', $raw->lastCall()['method']);
        $this->assertSame(['1', '2'], $raw->lastCall()['parameters']['tickets']);
    }

    public function test_photo_search_pages_yields_lazily_and_stops_at_total_pages(): void
    {
        $raw = new class implements RawApiServiceContract
        {
            /**
             * @var list<array{method: string, parameters: array<string, mixed>, options: ?RequestOptionsData}>
             */
            public array $calls = [];

            public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
            {
                $this->calls[] = compact('method', 'parameters', 'options');
                $page = (int) $parameters['page'];

                return new ApiResponseData(
                    ok: true,
                    data: ['photos' => ['photo' => [['id' => (string) $page]]]],
                    pagination: new PaginationData($page, 2, (int) $parameters['per_page'], 2),
                );
            }
        };
        $photos = new PhotoService($raw);

        $pages = $photos->searchPages(
            SearchPhotosData::from(['text' => 'sunset', 'perPage' => 50]),
            new PaginationOptionsData(perPage: 25),
            new RequestOptionsData(cacheTtl: 60),
        );

        $this->assertCount(0, $raw->calls);
        $collected = iterator_to_array($pages);

        $this->assertCount(2, $collected);
        $this->assertSame(1, $raw->calls[0]['parameters']['page']);
        $this->assertSame(25, $raw->calls[0]['parameters']['per_page']);
        $this->assertSame(2, $raw->calls[1]['parameters']['page']);
        $this->assertSame(60, $raw->calls[0]['options']?->cacheTtl);
    }

    public function test_photo_search_pages_respects_max_pages_and_empty_stop(): void
    {
        $raw = new class implements RawApiServiceContract
        {
            /**
             * @var list<array{method: string, parameters: array<string, mixed>, options: ?RequestOptionsData}>
             */
            public array $calls = [];

            public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
            {
                $this->calls[] = compact('method', 'parameters', 'options');

                return new ApiResponseData(
                    ok: true,
                    data: ['photos' => ['photo' => []]],
                    pagination: new PaginationData((int) $parameters['page'], 5, (int) $parameters['per_page'], 0),
                );
            }
        };
        $photos = new PhotoService($raw);

        $emptyStop = iterator_to_array($photos->searchPages(
            new SearchPhotosData,
            new PaginationOptionsData(maxPages: 3),
        ));
        $this->assertCount(1, $emptyStop);

        $continueEmpty = iterator_to_array($photos->searchPages(
            new SearchPhotosData,
            new PaginationOptionsData(maxPages: 2, stopWhenEmpty: false),
        ));
        $this->assertCount(2, $continueEmpty);
        $this->assertCount(3, $raw->calls);
    }

    public function test_services_reject_invalid_inputs(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new PhotoService(new FakeRawApiService))->addTags('1', []);
    }
}
