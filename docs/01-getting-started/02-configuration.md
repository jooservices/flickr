# Configuration

```php
use JOOservices\Flickr\Config\FlickrConfig;

$config = FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
    'callbackUrl' => $_ENV['FLICKR_CALLBACK_URL'] ?? null,
    'retryTimes' => 2,
]);
```

Defaults use Flickr's official REST, OAuth, upload, and replace endpoints.

When no `callbackUrl` is configured, OAuth request-token flows default to `oauth_callback=oob` for out-of-band authorization.

## Token storage

`FileTokenStore` persists tokens as JSON with `chmod 0600`. Wrap it with `EncryptedTokenStore` when libsodium is available and you want encrypted token files at rest:

```php
use JOOservices\Flickr\Auth\EncryptedTokenStore;
use JOOservices\Flickr\Auth\FileTokenStore;

$store = new EncryptedTokenStore(
    inner: new FileTokenStore('/path/to/token.json'),
    key: sodium_crypto_secretbox_keygen(),
);
```

## Transport retries

`retryTimes` configures how many times `jooservices/client` retries transient transport failures. Set `0` to disable retries. Retries exclude `POST` by default (Flickr mutations), so registry `HttpMethod` accuracy matters.

## Circuit breaker and rate limiting

Both are enabled by default and implemented by `jooservices/client` middleware — this SDK does not reimplement them.

```php
$config = FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
    'enableCircuitBreaker' => true,
    'enableRateLimit' => true,
    // Unofficial community default (~3600 requests/hour). Not an official Flickr guarantee.
    'rateLimitMaxTokens' => 3600,
    'rateLimitRefillPerSecond' => 1,
]);
```

Set `enableCircuitBreaker` / `enableRateLimit` to `false` to opt out.

### Rate-limit store concurrency

`JooClientTransport::fromConfig()` uses the client's default **in-memory** rate-limit store unless you pass a `RateLimitStoreInterface`. That default is **per-process only** (fine for CLI / single worker).

For multi-worker PHP-FPM apps you may pass `JOOservices\Client\Resilience\Storage\Psr16RateLimitStore` with a shared PSR-16 cache. Treat that as **best-effort**: PSR-16 has no atomic compare-and-swap, so concurrent workers can still race. Do not claim hard multi-worker quota enforcement from this layer alone.

```php
use JOOservices\Client\Resilience\Storage\Psr16RateLimitStore;
use JOOservices\Flickr\Client\JooClientTransport;

$transport = JooClientTransport::fromConfig(
    $config,
    new Psr16RateLimitStore($psr16Cache),
);
```

`withIdempotencyKey()` is intentionally **not** wired — Flickr's REST API does not document recognizing an `Idempotency-Key` header.
