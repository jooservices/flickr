# Search Public Photos

```php
$response = $flickr->photos()->search(SearchPhotosData::from([
    'text' => 'sunset',
    'perPage' => 20,
]));
```
