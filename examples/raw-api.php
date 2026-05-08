<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';

if ($apiKey === '' || $apiSecret === '') {
    fwrite(STDERR, 'Set FLICKR_API_KEY and FLICKR_API_SECRET.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
$response = $flickr->raw()->call('flickr.photos.search', [
    'text' => getenv('FLICKR_SEARCH_TEXT') ?: 'cats',
    'per_page' => 10,
]);

echo json_encode($response->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
