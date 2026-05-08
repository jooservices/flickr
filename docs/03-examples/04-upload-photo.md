# Upload Photo

```php
$result = $flickr->uploads()->upload(UploadPhotoData::from([
    'path' => '/tmp/photo.jpg',
    'title' => 'My photo',
]));
```
