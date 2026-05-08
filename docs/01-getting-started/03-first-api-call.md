# First API Call

```php
$response = $flickr->raw()->call('flickr.photos.search', [
    'text' => 'sunset',
    'per_page' => 10,
]);
```

For a typed wrapper, use `photos()->search(SearchPhotosData::from(...))`.
