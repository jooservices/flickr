<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$flickr = FlickrFactory::make(FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
]));
$flickr->tokens()->put(new AccessTokenData($_ENV['FLICKR_ACCESS_TOKEN'], $_ENV['FLICKR_ACCESS_TOKEN_SECRET']));

$result = $flickr->uploads()->upload(UploadPhotoData::from([
    'path' => $argv[1],
    'privacy' => Privacy::Private,
    'async' => true,
]));

var_dump($result->toArray());
