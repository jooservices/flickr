<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Services\FavoriteService;
use JOOservices\Flickr\Services\GroupsPoolsService;
use JOOservices\Flickr\Services\PeopleService;
use JOOservices\Flickr\Services\PhotoService;
use JOOservices\Flickr\Services\PhotosetService;
use JOOservices\Flickr\Services\PhotosUploadService;
use JOOservices\Flickr\Services\PlacesService;
use JOOservices\Flickr\Services\TagService;
use JOOservices\Flickr\Tests\Fakes\FakeRawApiService;
use JOOservices\Flickr\Tests\Fakes\PayloadRawApiService;
use JOOservices\Flickr\Tests\TestCase;

final class ServiceDataTest extends TestCase
{
    public function test_photo_service_data_helpers_hydrate_responses(): void
    {
        $raw = $this->rawWith([
            'flickr.photos.search' => ['photos' => ['photo' => [['id' => '1', 'title' => 'A']]]],
            'flickr.photos.getInfo' => ['photo' => ['id' => '2', 'secret' => 's']],
            'flickr.photos.getSizes' => ['sizes' => ['size' => [['label' => 'M', 'source' => 'u', 'width' => 1, 'height' => 2]]]],
            'flickr.photos.getExif' => ['photo' => ['exif' => ['model' => 'Canon']]],
        ]);
        $photos = new PhotoService($raw);

        $this->assertSame('1', $photos->searchData(SearchPhotosData::from(['text' => 'x']))[0]->id);
        $this->assertSame('2', $photos->getInfoData('2')->id);
        $this->assertSame('M', $photos->getSizesData('2')[0]->label);
        $this->assertSame('Canon', $photos->getExifData('2')->data['model']);
    }

    public function test_other_service_data_helpers_hydrate_responses(): void
    {
        $raw = $this->rawWith([
            'flickr.people.getInfo' => ['person' => ['nsid' => '1', 'username' => 'viet']],
            'flickr.people.getPhotos' => ['photos' => ['photo' => [['id' => '9']]]],
            'flickr.photosets.getList' => ['photosets' => ['photoset' => [['id' => '10', 'title' => 'Set']]]],
            'flickr.photosets.getPhotos' => ['photoset' => ['photo' => [['id' => '11']]]],
            'flickr.favorites.getList' => ['photos' => ['photo' => [['id' => '12']]]],
            'flickr.groups.pools.getPhotos' => ['photos' => ['photo' => [['id' => '13']]]],
            'flickr.tags.getHotList' => ['hottags' => ['tag' => [['_content' => 'sunset']]]],
            'flickr.places.find' => ['places' => ['place' => [['place_id' => 'woe:1', 'name' => 'SF']]]],
            'flickr.places.getInfo' => ['place' => ['place_id' => 'woe:2', 'name' => 'NYC']],
        ]);

        $this->assertSame('viet', (new PeopleService($raw))->getInfoData('1')->attributes['username']);
        $this->assertSame('9', (new PeopleService($raw))->getPhotosData()[0]->id);
        $this->assertSame('Set', (new PhotosetService($raw))->getListData()[0]->title);
        $this->assertSame('11', (new PhotosetService($raw))->getPhotosData('set')[0]->id);
        $this->assertSame('12', (new FavoriteService($raw))->getListData()[0]->id);
        $this->assertSame('13', (new GroupsPoolsService($raw))->getPhotosData()[0]->attributes['id']);
        $this->assertSame('sunset', (new TagService($raw))->getHotListData()[0]->value);
        $this->assertSame('SF', (new PlacesService($raw))->findData()[0]->name);
        $this->assertSame('NYC', (new PlacesService($raw))->getInfoData(['place_id' => 'woe:2'])->name);
    }

    /**
     * @param  array<string, array<string, mixed>>  $payloads
     */
    private function rawWith(array $payloads): RawApiServiceContract
    {
        return new PayloadRawApiService($payloads);
    }

    public function test_photos_upload_service_normalizes_ticket_parameters(): void
    {
        $raw = new FakeRawApiService;
        $service = new PhotosUploadService($raw);

        $service->checkTickets(['tickets' => [' 1 ', '2']]);

        $this->assertSame(['1', '2'], $raw->lastCall()['parameters']['tickets']);
    }
}
