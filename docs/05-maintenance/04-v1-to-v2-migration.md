# Migrating from v1 to v2

This guide covers breaking and notable changes in `jooservices/flickr` v2.0.0.

## Dependency requirements

| Package | v1 | v2 |
|---|---|---|
| PHP | `>=8.5` | `>=8.5` (unchanged; matches `jooservices/client` 2.x) |
| `jooservices/client` | `^1.4` | `^2.0` |
| `jooservices/dto` | `^1.2` | `^1.3` |
| `jooservices/exceptions` | — | `^0.5` (new required dependency) |

```bash
composer require jooservices/flickr:^2.0
```

## Exceptions

`FlickrException` now extends `JOOservices\Exceptions\Base\AbstractJOORuntimeException`.

`catch (FlickrException)` and `catch (ApiException)` continue to work. You can also catch the ecosystem marker:

```php
use JOOservices\Exceptions\Contracts\JOOExceptionInterface;

try {
    $flickr->photos()->search($query);
} catch (JOOExceptionInterface $e) {
    // any JOOservices package exception
}
```

`ApiException` gains `httpStatus(): ?int` and `retryable(): bool`. `apiCode()` is unchanged.

```php
} catch (ApiException $e) {
    if ($e->retryable()) {
        // backoff and retry
    }
}
```

## Transport resilience

Circuit breaker and client-side rate limiting are **enabled by default** via `jooservices/client`. Opt out:

```php
$config = new FlickrConfig(
    apiKey: $key,
    apiSecret: $secret,
    enableCircuitBreaker: false,
    enableRateLimit: false,
);
```

`retryTimes` still defaults to `0`. Retries still exclude `POST` by default.

Rate-limit default store is **in-memory / per-process**. See [Configuration](../01-getting-started/02-configuration.md).

Default `userAgent` is now `JOOservices Flickr SDK/2.0`.

## Upload tickets

`UploadTicketData::$complete` is now `?int` (Flickr status `0` / `1` / `2`), not `?bool`.

Prefer typed helpers:

```php
$tickets = $flickr->uploads()->checkTicketsData(['ticket-id']);
$outcome = $flickr->uploads()->ticketPoller()->waitForCompletion('ticket-id');
```

`TicketPoller` is **blocking** — use only from CLI, queue workers, or cron.
It applies the remaining poll deadline as the timeout for each ticket request.

If you implement `UploadServiceContract` yourself, update `checkTickets()` and
`checkTicketsData()` to accept the new optional `?RequestOptionsData $options = null`
argument. The SDK passes a per-request timeout through this argument while polling.

## Testing

Prefer `JOOservices\Flickr\Testing\FlickrFake` for application tests. `FakeFlickrTransport` remains for low-level transport tests but is deprecated for consumer use.

```php
$fake = FlickrFake::create();
$fake->respond('flickr.photos.search', ['photos' => [/* ... */]]);
$flickr = $fake->flickr();
```

## Introspection

```php
$info = $flickr->describe('flickr.photos.search');
```

Returns `null` for unregistered methods. Raw fallback for unknown methods is unchanged; failed calls may include a "Did you mean …?" hint.
