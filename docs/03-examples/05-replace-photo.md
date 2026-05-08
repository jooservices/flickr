# Replace Photo

```php
$result = $flickr->uploads()->replace(ReplacePhotoData::from([
    'path' => '/tmp/new.jpg',
    'photoId' => '123456',
]));
```
