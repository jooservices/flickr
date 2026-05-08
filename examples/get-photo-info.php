<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';
$photoId = $argv[1] ?? getenv('FLICKR_PHOTO_ID') ?: '';

if ($apiKey === '' || $apiSecret === '' || $photoId === '') {
    fwrite(STDERR, 'Set FLICKR_API_KEY, FLICKR_API_SECRET, and pass PHOTO_ID or set FLICKR_PHOTO_ID.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
$response = $flickr->photos()->getInfo($photoId);

echo json_encode($response->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
