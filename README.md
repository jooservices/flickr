# jooservices/flickr

[![CI](https://github.com/jooservices/flickr/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/jooservices/flickr/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/jooservices/flickr/graph/badge.svg)](https://codecov.io/gh/jooservices/flickr)
[![Quality gate status](https://sonarcloud.io/api/project_badges/measure?project=jooservices_flickr&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=jooservices_flickr)
[![OpenSSF Scorecard](https://api.securityscorecards.dev/projects/github.com/jooservices/flickr/badge)](https://securityscorecards.dev/viewer/?uri=github.com/jooservices/flickr)
[![PHP Version](https://img.shields.io/badge/PHP-8.5%2B-blue.svg)](https://www.php.net/)
[![Release](https://img.shields.io/badge/version-4.0.0-blue.svg)](CHANGELOG.md)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

PHP 8.5+, framework-agnostic Flickr SDK built on [`jooservices/client:^4.0`](https://github.com/jooservices/client) and
[`jooservices/dto:^3.0`](https://github.com/jooservices/dto). Complete 224-method REST registry, OAuth 1.0a,
multipart upload/replace with ticket polling, PSR-16 response caching, deterministic fakes — **no backward
compatibility with v2**.

## Install

```bash
composer require jooservices/flickr:^4.0
```

Runtime requirements are installed automatically: `jooservices/client` (PSR-18 HTTP, TLS/timeouts/retries),
`jooservices/dto`, `nyholm/psr7`. `ext-curl` powers client v4's default transport; you may inject any supported
client v4 transport instead.

## Quick start

```php
use JOOservices\Client\ClientBuilder;
use JOOservices\Flickr\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Dtos\Photos\SearchPhotosData;

$http = ClientBuilder::create()
    ->withTimeout(30)
    ->build();

$flickr = FlickrFactory::make(
    config: new FlickrConfig(apiKey: $key, apiSecret: $secret),
    http: $http,
);

// Typed priority workflow.
$page = $flickr->photos()->search(new SearchPhotosData(text: 'sunset', perPage: 20));

// Full known surface through the universal gateway.
$response = $flickr->api()->call('flickr.tags.getHotList', ['count' => 20]);

// Future/undocumented methods: explicit verb + explicit auth mode, never cached.
$response = $flickr->api()->raw(
    method: 'flickr.future.method',
    httpMethod: \JOOservices\Flickr\Enums\HttpMethod::Get,
    options: new \JOOservices\Flickr\Api\RawCallOptions(\JOOservices\Flickr\Enums\AuthenticationMode::Unauthenticated),
    parameters: ['query' => 'value'],
);

// Local metadata lookup.
$definition = $flickr->api()->describe('flickr.photos.search');
```

`FlickrFactory::makeDefault()` composes over client v4 defaults; prefer explicit `make()` with your own
`HttpClient` so TLS/proxy/timeout policy stays yours.

## Scope of this package vs client v4

| Concern | Owner |
| --- | --- |
| Endpoints, JSON REST contract, OAuth signing, upload XML, error codes | this package |
| TLS, proxy, timeouts, redirects implementation, retry/resilience, logging | `jooservices/client` v4 config |
| Public data mapping (DTOs/hydrators) | this package on dto v3 primitives |

The SDK never installs retry middleware and never retries POST mutations or uploads. Client v4's default retry
allowlist excludes POST; a consumer configuration that adds POST retries is unsupported for Flickr mutations.
Every Flickr request is sent with redirects disabled (`allowRedirects: false`) so signed credentials can never
follow an application redirect to another host. Endpoints are final package constants — host/path overrides are
not accepted (SSRF/signature-drift protection).

## Authentication (OAuth 1.0a)

```php
$pending = $flickr->oauth()->begin(AuthPermission::Write);   // immutable PendingAuthorization
// Persist $pending SERVER-SIDE bound to your logged-in user (10-minute lifetime),
// redirect the user to $pending->authorizationUrl, then on callback:
$token = $flickr->oauth()->complete(
    $pending,
    new OAuthCallback(oauthToken: $_GET['oauth_token'], verifier: $_GET['oauth_verifier']),
);
```

HMAC-SHA1 signing is isolated to Flickr's mandated OAuth flow. Request-token secrets never live in mutable SDK
state; access tokens go to your injected `TokenStore`. The package ships only `InMemoryTokenStore` (lost on
process exit, single-request/testing use only) and `NullTokenStore` (discards everything) — neither is suitable
for production. **You must supply your own `TokenStore` implementation that persists tokens encrypted at rest**
before shipping OAuth-authenticated calls; do not serialize `AccessTokenData` to disk or a database in plaintext.

## Upload, replace, tickets

```php
$result = $flickr->uploads()->upload('/path/photo.jpg', new UploadOptions(
    title: 'Sunset',
    tags: ['nature', 'big wave'],   // multi-word tags are quoted automatically
    async: false,
));

$status = $flickr->uploads()->uploadStatus();          // advisory quota, never blocks uploads
$results = $factoryPoller->poll(['ticket-id'], 1000, 60_000); // bounded poller; not for web requests
```

Upload quota is advisory (`people.getUploadStatus`); archive-era `getLimits` enforcement is intentionally gone.
Failed uploads surface Flickr's safe error metadata (duplicate-photo fields, `non_pro_desktop_upload_wait_time`)
and are never auto-retried.

## Caching

Inject any PSR-16 cache; only successful unauthenticated GET calls marked cacheable in the frozen registry are
stored (parsed metadata only — never photo binaries). Authenticated calls, raw calls, mutations, uploads and
ticket polling bypass cache absolutely. Backend failures throw a clear `CacheBackendException`; nothing is ever
fabricated from a broken backend.

## Testing without network

```php
$fake = FlickrFake::create();                       // builds on client v4 fakes
$client = ClientBuilder::create()->build();
$flickr = FlickrFactory::make($config, $client);
$fake->queueJson(['stat' => 'ok', ...]);
// ... exercise the SDK ...
$fake->assertCall(0, 'flickr.photos.search', ['text' => 'sunset']);
$fake->close();                                     // call in tearDown()
```

`FlickrFake::create()` flips **process-wide** fake state in `jooservices/client`, not state scoped to one
`Flickr` instance — every `Flickr`/`FlickrFactory::make()` call anywhere in the process returns queued fake
responses until `close()` runs. Never let this reach a production bootstrap path; always pair `create()` with
`close()`, including on a failed test (`tearDown()`, not just the end of a happy-path test).

## Long-running processes

A `Flickr` instance is safe to keep alive and reuse across many requests in a persistent-process runtime
(Swoole, RoadRunner, a queue worker) — its internal secret-redaction tracking is capped, not unbounded, so
memory and per-call cost stay flat regardless of how many requests the instance has served. The one cost of
reuse is that `FlickrMethodRegistry` (the 224-method definition table) is rebuilt on every `FlickrFactory::make()`
call, not memoized — construct one `Flickr` instance and hold onto it rather than calling `make()` per request.

## Error handling

Typed exceptions from one hierarchy (`FlickrException`): `ConfigurationException`,
`AuthenticationException`, `AuthorizationException`, `ApiException` (+`UnavailableMethodException`),
`TransportException`, `RateLimitException` (with parsed `Retry-After`), `InvalidResponseException`,
`UploadException`, `CacheBackendException`. With `throwOnApiError`, Flickr codes map: `98` → auth,
`99` → permissions, otherwise `ApiException`. Without it you receive an `ApiResponseData` envelope with
`ok=false` and a safe `ApiErrorData`.

Legacy pre-OAuth `flickr.auth.*`, legacy `flickr.auth.oauth.*` and discontinued Panda methods remain documented
via `$flickr->legacyAuth()` / `$flickr->panda()` and fail clearly before any network traffic.

**This SDK never retries a failed call automatically** — no retry middleware, no built-in backoff. A
`RateLimitException` carries a parsed `retryAfterSeconds` (`null` if Flickr didn't send one), and
`ApiException::$retryable` is `true` only for Flickr's generic transient code `105`; every other `ApiException`
represents a request that will fail again unchanged if retried. Deciding whether, how many times, and with what
backoff to retry is entirely the caller's responsibility.

## Flickr API terms — non-negotiable consumer duties

This SDK performs protocol I/O only; it cannot inject page headers, limit rendered galleries or purge your
caches. Consumer applications MUST:

1. Respect photo-owner license restrictions — API responses and photo URLs grant no reuse rights.
2. Show **no more than 30 Flickr photos per page** and never treat Flickr as generic image hosting.
3. Display Flickr's required non-endorsement disclaimer and required attribution/header where terms apply; do
   not imply endorsement or use Flickr branding without permission.
4. Publish your own privacy disclosure, reflect photos becoming private promptly, and remove owner-requested
   content within the terms' stated window.
5. Cache only API metadata (this package enforces the no-binary guarantee); invalidation and UI/legal compliance
   stay your responsibility.

## Security

Report vulnerabilities per [`SECURITY.md`](SECURITY.md). Secrets (API key/secret, token secret, verifier,
signature) are redacted from SDK exception messages; no secret is written to caches, logs or fakes.

## Development (Docker-only)

```bash
make install      # build image + composer install
make test         # phpunit
make lint         # pint, phpcs, phpstan(max+strict), phpmd, cs-fixer
make registry     # verify frozen 224-method manifest provenance
make ci           # validate + verify + lint + coverage(>=85%) + smoke
```

Real-network tests require explicit `FLICKR_REAL_TESTS=1` plus credentials and never run destructive
operations without disposable-account approval. Generated sources live under `resources/` +
`tools/generate-api-surface.php`; regenerate with `composer generate:api-index` and commit the diff.

## License

MIT — see [LICENSE](LICENSE).
