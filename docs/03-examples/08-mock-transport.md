# Mock Transport

Use `FakeFlickrTransport` to test application code without calling Flickr.

```php
use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
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

$response = $flickr->photos()->search(SearchPhotosData::from([
    'text' => 'cat',
]));

$transport->assertSentMethod('flickr.photos.search');
```

The fake records REST query requests, form POST requests, and multipart upload/replace requests. It is intended for local tests and examples only; production code should use the default `JooClientTransport`.
