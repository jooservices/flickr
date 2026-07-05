# People

Implemented V1 methods:

- `getInfo` / `getInfoData`
- `getUploadStatus`
- `getPhotos` / `getPhotosData`

## Typed people info

```php
$person = $flickr->people()->getInfoData('12345678@N00');
```

## Lazy photo pagination

```php
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;

foreach ($flickr->people()->getPhotosPages(
    ['user_id' => '12345678@N00'],
    new PaginationOptionsData(maxPages: 5, perPage: 50),
) as $page) {
    // inspect $page->data['photos']['photo']
}
```
