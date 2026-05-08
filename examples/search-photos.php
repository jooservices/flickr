<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$flickr = FlickrFactory::make(FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
]));

$response = $flickr->photos()->search(SearchPhotosData::from([
    'text' => 'sunset',
    'tags' => ['landscape'],
    'perPage' => 20,
]));

var_dump($response->toArray());
