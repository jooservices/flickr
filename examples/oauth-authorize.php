<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$flickr = FlickrFactory::make(FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
    'callbackUrl' => $_ENV['FLICKR_CALLBACK_URL'],
]));

$requestToken = $flickr->auth()->requestToken(AuthPermission::Write);

echo $flickr->auth()->authorizationUrl($requestToken, AuthPermission::Write).PHP_EOL;
