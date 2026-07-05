# Favorites

Favorite wrappers are available through `favorites()`.

Example:

```php
$flickr->favorites()->add(['photo_id' => '123456']);
```

## Typed favorites list

```php
$list = $flickr->favorites()->getListData(['user_id' => '12345678@N00']);
```

## Lazy favorites pagination

```php
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;

foreach ($flickr->favorites()->getListPages(
    ['user_id' => '12345678@N00'],
    new PaginationOptionsData(maxPages: 3, perPage: 50),
) as $page) {
    // inspect $page->data['photos']['photo']
}
```
