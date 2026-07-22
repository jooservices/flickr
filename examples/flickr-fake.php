<?php

declare(strict_types=1);

/**
 * Example: FlickrFake for consumer tests.
 */

require __DIR__.'/../vendor/autoload.php';

use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Testing\FlickrFake;

$fake = FlickrFake::create();
$fake->respond('flickr.photos.search', [
    'photos' => [
        'page' => 1,
        'pages' => 1,
        'perpage' => 1,
        'total' => 0,
        'photo' => [],
    ],
]);

$response = $fake->flickr()->photos()->search(SearchPhotosData::from([
    'text' => 'cat',
    'perPage' => 1,
]));

$fake->assertCalled('flickr.photos.search');

echo $response->ok ? "ok\n" : "fail\n";
