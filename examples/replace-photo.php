<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';
$accessToken = getenv('FLICKR_ACCESS_TOKEN') ?: '';
$accessTokenSecret = getenv('FLICKR_ACCESS_TOKEN_SECRET') ?: '';
$path = $argv[1] ?? getenv('FLICKR_REPLACE_PATH') ?: '';
$photoId = $argv[2] ?? getenv('FLICKR_PHOTO_ID') ?: '';

if ($apiKey === '' || $apiSecret === '' || $accessToken === '' || $accessTokenSecret === '' || $path === '' || $photoId === '') {
    fwrite(STDERR, 'Set Flickr credentials, OAuth access token, file path, and photo id.'.PHP_EOL);
    exit(1);
}

if (getenv('FLICKR_CONFIRM_REPLACE') !== 'yes') {
    fwrite(STDERR, 'Replace mutates your Flickr account. Set FLICKR_CONFIRM_REPLACE=yes to continue.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
$flickr->tokens()->put(new AccessTokenData($accessToken, $accessTokenSecret));

$result = $flickr->uploads()->replace(ReplacePhotoData::from([
    'path' => $path,
    'photoId' => $photoId,
    'async' => true,
]));

echo json_encode($result->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
