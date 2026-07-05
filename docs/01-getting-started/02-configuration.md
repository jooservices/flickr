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

`retryTimes` configures how many times `jooservices/client` retries transient transport failures. Set `0` to disable retries.
