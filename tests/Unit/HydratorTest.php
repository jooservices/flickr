<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\Hydrators\FavoriteHydrator;
use JOOservices\Flickr\Hydrators\PeopleHydrator;
use JOOservices\Flickr\Hydrators\PhotoHydrator;
use JOOservices\Flickr\Hydrators\PhotosetHydrator;
use JOOservices\Flickr\Hydrators\PlaceHydrator;
use JOOservices\Flickr\Hydrators\TagHydrator;
use JOOservices\Flickr\Tests\TestCase;

final class HydratorTest extends TestCase
{
    public function test_photo_hydrator_maps_search_get_info_sizes_and_exif(): void
    {
        $hydrator = new PhotoHydrator;

        $search = $hydrator->photos(new ApiResponseData(
            ok: true,
            data: ['photos' => ['photo' => [['id' => '1', 'title' => 'Sunset', 'owner' => 'user-1']]]],
        ));
        $this->assertSame('1', $search[0]->id);
        $this->assertSame('Sunset', $search[0]->title);

        $info = $hydrator->photoInfo(new ApiResponseData(
            ok: true,
            data: ['photo' => ['id' => '2', 'secret' => 'abc', 'server' => '1']],
        ));
        $this->assertSame('2', $info->id);
        $this->assertSame('abc', $info->attributes['secret']);

        $sizes = $hydrator->sizes(new ApiResponseData(
            ok: true,
            data: ['sizes' => ['size' => [['label' => 'Medium', 'source' => 'https://example.test/m.jpg', 'width' => 240, 'height' => 180]]]],
        ));
        $this->assertSame('Medium', $sizes[0]->label);

        $exif = $hydrator->exif(new ApiResponseData(
            ok: true,
            data: ['photo' => ['exif' => ['model' => 'Canon']]],
        ));
        $this->assertSame('Canon', $exif->data['model']);
    }

    public function test_people_photoset_favorite_tag_and_place_hydrators(): void
    {
        $people = (new PeopleHydrator)->person(new ApiResponseData(
            ok: true,
            data: ['person' => ['nsid' => '123', 'username' => 'viet']],
        ));
        $this->assertSame('123', $people->id);
        $this->assertSame('viet', $people->attributes['username']);

        $photosets = (new PhotosetHydrator)->list(new ApiResponseData(
            ok: true,
            data: ['photosets' => ['photoset' => [['id' => '10', 'title' => 'Trip']]]],
        ));
        $this->assertSame('Trip', $photosets[0]->title);

        $favorites = (new FavoriteHydrator)->list(new ApiResponseData(
            ok: true,
            data: ['photos' => ['photo' => [['id' => '55', 'title' => 'Fav']]]],
        ));
        $this->assertSame('55', $favorites[0]->id);

        $tags = (new TagHydrator)->hotList(new ApiResponseData(
            ok: true,
            data: ['hottags' => ['tag' => [['_content' => 'sunset']]]],
        ));
        $this->assertSame('sunset', $tags[0]->value);

        $place = (new PlaceHydrator)->place(new ApiResponseData(
            ok: true,
            data: ['place' => ['place_id' => 'woe:1', 'name' => 'San Francisco']],
        ));
        $this->assertSame('woe:1', $place->placeId);
        $this->assertSame('San Francisco', $place->name);
    }
}
