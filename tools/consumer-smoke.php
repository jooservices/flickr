<?php

declare(strict_types=1);

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Flickr\Api\RawCallOptions;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Dtos\Photos\SearchPhotosData;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\AuthenticationMode;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Exceptions\FlickrException;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Testing\FlickrFake;
use JOOservices\Flickr\Upload\UploadOptions;

require __DIR__ . '/../vendor/autoload.php';

$fails = 0;
$check = static function (bool $condition, string $label) use (&$fails): void {
    echo ($condition ? 'ok   ' : 'FAIL ') . $label . "\n";

    if ($condition === false) {
        ++$fails;
    }
};

try {
    $fake = FlickrFake::create();
    $config = new FlickrConfig(apiKey: 'smoke-key', apiSecret: 'smoke-secret');
    $http = ClientBuilder::create()->build();
    $tokens = new \JOOservices\Flickr\Auth\InMemoryTokenStore();
    $tokens->put(new \JOOservices\Flickr\Auth\AccessTokenData('tok', 'sec', AuthPermission::Write));
    $flickr = FlickrFactory::make($config, $http, tokens: $tokens);

    $fake->queueJson(['stat' => 'ok', 'photos' => ['page' => 1, 'pages' => 1, 'total' => '1', 'photo' => [
        ['id' => '42', 'owner' => 'me', 'title' => 'Smoke', 'secret' => 'sec', 'server' => 'sv', 'farm' => 6],
    ]]]);
    $search = $flickr->photos()->search(new SearchPhotosData(text: 'sunset', perPage: 10));
    $check($search->photos[0]->id === '42', 'typed photos.search');
    $fake->assertCall(0, 'flickr.photos.search', ['text' => 'sunset']);

    $fake->queueJson(['stat' => 'ok']);
    $raw = $flickr->api()->raw('flickr.future.method', HttpMethod::Get, new RawCallOptions(AuthenticationMode::Unauthenticated));
    $check($raw->ok, 'explicit raw fallback');

    $fake->queueRaw(new \JOOservices\Flickr\Dtos\Common\RawResponseData(200, [], 'oauth_callback_confirmed=true&oauth_token=req&oauth_token_secret=reqs'));
    $pending = $flickr->oauth()->begin(AuthPermission::Write);
    $check(str_contains($pending->authorizationUrl, 'perms=write'), 'oauth begin authorization url');

    $path = tempnam(sys_get_temp_dir(), 'smoke');
    file_put_contents((string) $path, 'bytes');
    $fake->queueXml('<rsp stat="ok"><photoid>7</photoid></rsp>');
    $upload = $flickr->uploads()->upload((string) $path, new UploadOptions(title: 'x'));
    unlink((string) $path);
    $check($upload->photoId === '7', 'multipart upload');

    $info = $flickr->api()->describe('flickr.photos.upload.checkTickets');
    $check($info !== null && $info->cacheable === false, 'ticket polling never cacheable');

    $legacy = $flickr->legacyAuth()->getToken();
} catch (FlickrException $expected) {
    echo get_class($expected) . ": " . $expected->getMessage() . "\n";
    $check($expected instanceof \JOOservices\Flickr\Exceptions\UnavailableMethodException, 'legacy auth fails before network');
} finally {
    ClientBuilder::clearFake();
}

if ($fails > 0) {
    fwrite(STDERR, "Consumer smoke failed with {$fails} failure(s).\n");
    exit(1);
}

echo "Consumer smoke OK.\n";
