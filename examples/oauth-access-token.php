<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';
$oauthToken = $argv[1] ?? getenv('FLICKR_REQUEST_TOKEN') ?: '';
$oauthTokenSecret = $argv[2] ?? getenv('FLICKR_REQUEST_TOKEN_SECRET') ?: '';
$oauthVerifier = $argv[3] ?? getenv('FLICKR_OAUTH_VERIFIER') ?: '';

if ($apiKey === '' || $apiSecret === '' || $oauthToken === '' || $oauthTokenSecret === '' || $oauthVerifier === '') {
    fwrite(STDERR, 'Set Flickr credentials, request token, request token secret, and oauth verifier.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
$accessToken = $flickr->auth()->accessToken($oauthToken, $oauthVerifier, $oauthTokenSecret);

echo json_encode($accessToken->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
fwrite(STDERR, 'Store tokens securely. Do not commit them.'.PHP_EOL);
