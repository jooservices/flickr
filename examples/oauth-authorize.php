<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';
$callbackUrl = getenv('FLICKR_CALLBACK_URL') ?: null;

if ($apiKey === '' || $apiSecret === '') {
    fwrite(STDERR, 'Set FLICKR_API_KEY and FLICKR_API_SECRET.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret, callbackUrl: $callbackUrl));
$requestToken = $flickr->auth()->requestToken(AuthPermission::Write);

echo 'Open this URL and approve write permission:'.PHP_EOL;
echo $flickr->auth()->authorizationUrl($requestToken, AuthPermission::Write).PHP_EOL.PHP_EOL;
echo 'Save these temporary values for oauth-access-token.php:'.PHP_EOL;
echo 'FLICKR_REQUEST_TOKEN='.$requestToken->oauthToken.PHP_EOL;
echo 'FLICKR_REQUEST_TOKEN_SECRET='.$requestToken->oauthTokenSecret.PHP_EOL;
