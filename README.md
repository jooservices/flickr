# JOOservices Flickr SDK

[![CI](https://github.com/jooservices/flickr/actions/workflows/ci.yml/badge.svg)](https://github.com/jooservices/flickr/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-%3E%3D8.5-777bb4)

`jooservices/flickr` is a pure PHP, framework-agnostic Flickr SDK for PHP 8.5+. It is not a Laravel package and intentionally does not ship service providers, facades, routes, migrations, config publishing, or Artisan commands.

The SDK uses `jooservices/dto` for public data objects and `jooservices/client` for HTTP transport. Official Flickr documentation remains the source of truth for API method behavior: https://www.flickr.com/services/api/

Current coverage:

- all 224 official REST API methods from the Flickr API index have method-registry entries;
- all 224 official REST API methods have service wrapper methods;
- core workflows also have friendlier DTO-first wrappers where useful;
- raw fallback remains available for custom parameters and future methods.

## Install

```bash
composer require jooservices/flickr
```

## Configure

```php
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;

$flickr = FlickrFactory::make(FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
    'callbackUrl' => $_ENV['FLICKR_CALLBACK_URL'] ?? null,
]));
```

## First Public Search

```php
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;

$response = $flickr->photos()->search(SearchPhotosData::from([
    'text' => 'sunset',
    'tags' => ['landscape'],
    'perPage' => 20,
]));
```

`flickr.photos.search` can run unauthenticated for public photos. Private and semi-private results require OAuth read permission.

For lazy public search pagination, use `searchPages()`:

```php
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;

foreach ($flickr->photos()->searchPages(
    SearchPhotosData::from(['text' => 'sunset']),
    new PaginationOptionsData(maxPages: 3, perPage: 50),
) as $page) {
    // $page is ApiResponseData
}
```

## Raw API Fallback

```php
$response = $flickr->raw()->call('flickr.photos.search', [
    'text' => 'cats',
    'per_page' => 10,
]);
```

Unknown methods are still allowed so the package can call future Flickr methods before this package is updated.

## Full API Method Wrappers

Every method in the official Flickr method index is available through a service wrapper. Wrappers that do not yet have a specialized DTO accept an associative parameter array and return `ApiResponseData`.

Examples:

```php
$hotTags = $flickr->tags()->getHotList(['count' => 20]);

$recent = $flickr->photos()->getRecent([
    'per_page' => 20,
    'extras' => ['url_m', 'owner_name'],
]);

$favorite = $flickr->favorites()->add([
    'photo_id' => '123456',
]);

$location = $flickr->photosGeo()->getLocation([
    'photo_id' => '123456',
]);
```

The method registry stores docs URL, auth requirement, OAuth permission, cacheability, and GET/POST metadata scraped from the official method docs.

## OAuth 1.0a

```php
use JOOservices\Flickr\Enums\AuthPermission;

$requestToken = $flickr->auth()->requestToken(AuthPermission::Write);
$authorizationUrl = $flickr->auth()->authorizationUrl($requestToken, AuthPermission::Write);

// After Flickr redirects back with oauth_token and oauth_verifier:
$accessToken = $flickr->auth()->accessToken($oauthToken, $oauthVerifier);
$flickr->tokens()->put($accessToken);
```

Flickr supports HMAC-SHA1 for OAuth request signing.

## Upload, Replace, And Async Tickets

Upload and replace are separate from normal REST calls because Flickr sends binary files to `up.flickr.com`.

```php
use JOOservices\Flickr\DTO\Upload\UploadPhotoData;
use JOOservices\Flickr\Enums\Privacy;

$result = $flickr->uploads()->upload(UploadPhotoData::from([
    'path' => '/tmp/photo.jpg',
    'title' => 'My photo',
    'tags' => ['php', 'flickr'],
    'privacy' => Privacy::Private,
    'async' => true,
]));

$ticketResponse = $flickr->uploads()->checkTickets([$result->ticketId]);
```

Replace:

```php
use JOOservices\Flickr\DTO\Upload\ReplacePhotoData;

$result = $flickr->uploads()->replace(ReplacePhotoData::from([
    'path' => '/tmp/new-photo.jpg',
    'photoId' => '123456',
    'async' => true,
]));
```

Upload and replace require OAuth write permission. Delete requires delete permission. The SDK builds multipart requests with a readable file stream and closes that handle after the transport request completes.

## Error Handling

Normal API responses are mapped to `ApiResponseData`. Flickr `stat=fail` responses return `ok=false` with `ApiErrorData` unless request options set `throwOnApiError`. Malformed, empty, or structurally invalid responses throw `InvalidResponseException`. Transport failures throw `TransportException`.

## Cache

V1 includes `NullCache`, `Psr16Cache`, and `CacheKeyResolver`. Runtime caching is disabled by default because `FlickrFactory` uses `NullCache` unless a cache adapter is passed.

When a cache adapter is passed, only public cacheable GET REST calls can be cached. Mutation, auth, OAuth, upload, replace, upload ticket polling, authenticated options, auth-required methods, POST methods, and Flickr `stat=fail` responses are never cached by default.

```php
use JOOservices\Flickr\Cache\Psr16Cache;

$flickr = FlickrFactory::make(
    config: $config,
    cache: new Psr16Cache($psr16Cache),
);
```

## XML Support

JSON is the primary supported REST response format. XML parsing exists for Flickr upload/replace responses and has limited REST response parsing for compatibility, but REST XML should be treated as experimental unless a workflow has explicit tests.

## Testing

Normal tests do not call Flickr:

```bash
composer test
composer lint:all
composer check
```

Use the public fake transport to test application code without network calls:

```php
use JOOservices\Flickr\Client\FakeFlickrTransport;

$transport = FakeFlickrTransport::new()->pushJson([
    'stat' => 'ok',
    'photos' => ['page' => 1, 'pages' => 1, 'perpage' => 1, 'total' => 0, 'photo' => []],
]);

$flickr = FlickrFactory::make(
    config: new FlickrConfig('test-key', 'test-secret'),
    transport: $transport,
);
```

Real API tests are opt-in:

```bash
FLICKR_REAL_TESTS=true \
FLICKR_API_KEY=... \
FLICKR_API_SECRET=... \
FLICKR_ACCESS_TOKEN=... \
FLICKR_ACCESS_TOKEN_SECRET=... \
composer test -- --filter RealFlickrTest
```

## Docs

See [docs/README.md](docs/README.md) for architecture, getting started, user guide, examples, testing, release readiness, and V2 gaps.
