# Risks, Legacy, And Gaps

Current gaps:

- All official Flickr REST methods have wrappers, but most wrappers still accept associative parameter arrays rather than dedicated request DTOs.
- XML normal API support is basic and JSON is the primary supported response format.
- Upload file type validation is intentionally limited to local file safety; Flickr remains the authority for accepted media formats.
- Optional runtime caching is intentionally limited to public cacheable GET REST calls.
- Typed response hydration currently covers the priority methods listed below; remaining wrappers still return `ApiResponseData`.
- No concurrency helper is exposed because the current public transport contract only models single requests.

## Typed response coverage (v1.1.0)

Priority methods now expose `*Data()` helpers backed by `src/Hydrators/`:

- `flickr.photos.search` → `PhotoService::searchData()`
- `flickr.photos.getInfo` → `PhotoService::getInfoData()`
- `flickr.photos.getSizes` → `PhotoService::getSizesData()`
- `flickr.photos.getExif` → `PhotoService::getExifData()`
- `flickr.people.getInfo` → `PeopleService::getInfoData()`
- `flickr.people.getPhotos` → `PeopleService::getPhotosData()`
- `flickr.photosets.getList` → `PhotosetService::getListData()`
- `flickr.photosets.getPhotos` → `PhotosetService::getPhotosData()`
- `flickr.favorites.getList` → `FavoriteService::getListData()`
- `flickr.groups.pools.getPhotos` → `GroupsPoolsService::getPhotosData()`
- `flickr.tags.getHotList` → `TagService::getHotListData()`
- `flickr.places.*` → `PlacesService::findData()` and `getInfoData()`

## Pagination helpers

Generic lazy pagination is available through `Paginator` and service helpers:

- `PhotoService::searchPages()`
- `PeopleService::getPhotosPages()`
- `FavoriteService::getListPages()`

## Future expansion

Do not DTO-wrap all 224 methods in one sweep. Expand typed request and response objects only where the workflow is high-value and hydrators can be tested well. Keep raw fallback support intact.
