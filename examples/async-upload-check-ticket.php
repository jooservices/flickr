<?php

declare(strict_types=1);

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\FlickrFactory;

require __DIR__.'/../vendor/autoload.php';

$apiKey = getenv('FLICKR_API_KEY') ?: '';
$apiSecret = getenv('FLICKR_API_SECRET') ?: '';
$accessToken = getenv('FLICKR_ACCESS_TOKEN') ?: '';
$accessTokenSecret = getenv('FLICKR_ACCESS_TOKEN_SECRET') ?: '';
$ticketId = $argv[1] ?? getenv('FLICKR_UPLOAD_TICKET_ID') ?: '';

if ($apiKey === '' || $apiSecret === '' || $accessToken === '' || $accessTokenSecret === '' || $ticketId === '') {
    fwrite(STDERR, 'Set Flickr credentials, OAuth access token, and pass ticket id or set FLICKR_UPLOAD_TICKET_ID.'.PHP_EOL);
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig($apiKey, $apiSecret));
$flickr->tokens()->put(new AccessTokenData($accessToken, $accessTokenSecret));

$response = $flickr->uploads()->checkTickets([$ticketId]);

echo json_encode($response->toArray(), JSON_PRETTY_PRINT).PHP_EOL;
