<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Contracts\Client\FlickrUploadClientContract;
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

    public function test_services_reject_invalid_inputs(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new PhotoService(new FakeRawApiService))->addTags('1', []);
    }
}
