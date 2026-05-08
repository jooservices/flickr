<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';
$accessToken = getenv('FLICKR_ACCESS_TOKEN') ?: '';
$accessTokenSecret = getenv('FLICKR_ACCESS_TOKEN_SECRET') ?: '';
$path = $argv[1] ?? getenv('FLICKR_UPLOAD_PATH') ?: '';

if ($apiKey === '' || $apiSecret === '' || $accessToken === '' || $accessTokenSecret === '' || $path === '') {
    fwrite(STDERR, 'Set Flickr credentials, OAuth access token, and pass a file path or set FLICKR_UPLOAD_PATH.'.PHP_EOL);
    exit(1);
}

if (getenv('FLICKR_CONFIRM_UPLOAD') !== 'yes') {
    fwrite(STDERR, 'Upload mutates your Flickr account. Set FLICKR_CONFIRM_UPLOAD=yes to continue.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
$flickr->tokens()->put(new AccessTokenData($accessToken, $accessTokenSecret));

$result = $flickr->uploads()->upload(UploadPhotoData::from([
    'path' => $path,
    'title' => getenv('FLICKR_UPLOAD_TITLE') ?: null,
    'privacy' => Privacy::Private,
    'async' => true,
]));

echo json_encode($result->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
