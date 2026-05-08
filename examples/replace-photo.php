<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$flickr = FlickrFactory::make(FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
]));
$flickr->tokens()->put(new AccessTokenData($_ENV['FLICKR_ACCESS_TOKEN'], $_ENV['FLICKR_ACCESS_TOKEN_SECRET']));

$result = $flickr->uploads()->replace(ReplacePhotoData::from([
    'path' => $argv[1],
    'photoId' => $argv[2],
    'async' => true,
]));

var_dump($result->toArray());
