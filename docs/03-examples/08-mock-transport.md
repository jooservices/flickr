# Mock Transport / FlickrFake

Prefer `FlickrFake` for application tests (built on `jooservices/client` fakes):

```php
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Testing\FlickrFake;

$fake = FlickrFake::create();
$fake->respond('flickr.photos.search', [
    'photos' => [
        'page' => 1,
        'pages' => 1,
        'perpage' => 1,
        'total' => 0,
        'photo' => [],
    ],
]);

$flickr = $fake->flickr();
$response = $flickr->photos()->search(SearchPhotosData::from([
    'text' => 'cat',
]));

$fake->assertCalled('flickr.photos.search', ['text' => 'cat']);
```

## Low-level transport fake

`FakeFlickrTransport` remains available for transport-layer unit tests, but is deprecated for consumer application tests.

```php
use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;

$transport = FakeFlickrTransport::new()->pushJson([
    'stat' => 'ok',
    'photos' => [
        'page' => 1,
        'pages' => 1,
        'perpage' => 1,
        'total' => 0,
        'photo' => [],
    ],
]);

$flickr = FlickrFactory::make(
    config: new FlickrConfig('test-key', 'test-secret'),
    transport: $transport,
);
```
