# Custom Cache

Implement `FlickrCacheContract` when application code needs a package-local cache adapter.

```php
use JOOservices\Flickr\Contracts\Cache\FlickrCacheContract;

final class ArrayFlickrCache implements FlickrCacheContract
{
    private array $items = [];

    public function get(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function put(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->items[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($this->items[$key]);
    }
}
```

See `examples/custom-cache.php` for a runnable minimal script.
