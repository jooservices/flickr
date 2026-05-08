# Configuration

```php
use JOOservices\Flickr\Config\FlickrConfig;

$config = FlickrConfig::from([
    'apiKey' => $_ENV['FLICKR_API_KEY'],
    'apiSecret' => $_ENV['FLICKR_API_SECRET'],
    'callbackUrl' => $_ENV['FLICKR_CALLBACK_URL'] ?? null,
]);
```

Defaults use Flickr's official REST, OAuth, upload, and replace endpoints.
