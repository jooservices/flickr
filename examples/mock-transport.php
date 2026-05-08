<?php

declare(strict_types=1);

use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$transport = FakeFlickrTransport::new()->pushJson([
    'stat' => 'ok',
    'photos' => [
        'page' => 1,
        'pages' => 1,
        'perpage' => 1,
        'total' => 0,
        'photo' => [],
    ],
]);
$flickr = FlickrFactory::make(new FlickrConfig('test-key', 'test-secret'), transport: $transport);
$response = $flickr->photos()->search(SearchPhotosData::from(['text' => 'cat']));

$transport->assertSentMethod('flickr.photos.search');

echo json_encode([
    'response' => $response->toArray(),
    'last_request' => $transport->lastRequest(),
], JSON_PRETTY_PRINT).PHP_EOL;
