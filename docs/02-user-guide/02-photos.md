# Photos

Implemented V1 methods:

- `search`
- `getInfo`
- `getSizes`
- `getExif`
- `setMeta`
- `setTags`
- `addTags`
- `removeTag`
- `delete`

## Lazy Search Pagination

`searchPages()` yields `ApiResponseData` pages lazily and stops at Flickr's reported final page, an empty `photo` list when enabled, or the configured maximum page count.

```php
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;

foreach ($flickr->photos()->searchPages(
    SearchPhotosData::from(['text' => 'sunset']),
    new PaginationOptionsData(maxPages: 3, perPage: 50),
) as $page) {
    // inspect $page->data['photos']['photo']
}
```
