# Photos

Implemented V1 methods:

- `search` / `searchData`
- `getInfo` / `getInfoData`
- `getSizes` / `getSizesData`
- `getExif` / `getExifData`
- `setMeta`
- `setTags`
- `addTags`
- `removeTag`
- `delete`

## Typed search

`searchData()` returns a hydrated `SearchPhotosResultData` object:

```php
use JOOservices\Flickr\DTO\Photos\SearchPhotosData;
use JOOservices\Flickr\Enums\License;
use JOOservices\Flickr\Enums\PhotoExtra;
use JOOservices\Flickr\Enums\PrivacyFilter;
use JOOservices\Flickr\Enums\SortOrder;

$result = $flickr->photos()->searchData(SearchPhotosData::from([
    'text' => 'sunset',
    'license' => License::Attribution,
    'privacyFilter' => PrivacyFilter::PublicPhotos,
    'sort' => SortOrder::DatePostedDescending,
    'extras' => [PhotoExtra::UrlMedium, PhotoExtra::OwnerName],
    'perPage' => 20,
]));
```

## Photo URLs

Build Flickr image URLs from photo metadata with `PhotoUrlBuilder`:

```php
use JOOservices\Flickr\Enums\PhotoSize;
use JOOservices\Flickr\Support\PhotoUrlBuilder;

$url = PhotoUrlBuilder::forPhoto($photoData)->url(PhotoSize::Medium640);
```

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

## Typed photo info

```php
$photo = $flickr->photos()->getInfoData('123456');
$sizes = $flickr->photos()->getSizesData('123456');
$exif = $flickr->photos()->getExifData('123456');
```
