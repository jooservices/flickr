# Risks, Legacy, And Gaps

Current gaps:

- All official Flickr REST methods have wrappers, but most wrappers accept associative parameter arrays rather than dedicated request DTOs.
- XML normal API support is basic and JSON is the primary supported response format.
- Upload file type validation is intentionally limited to local file safety; Flickr remains the authority for accepted media formats.
- Optional runtime caching is intentionally limited to public cacheable GET REST calls.
- Pagination helpers currently cover `photos()->searchPages()` only.
- No concurrency helper is exposed because the current public transport contract only models single requests.

## DTO-first roadmap

Do not DTO-wrap all 224 methods in one sweep. Expand typed request and response objects where the workflow is high-value and the mapper can be tested well.

Priority candidates:

- `flickr.photos.search`
- `flickr.photos.getInfo`
- `flickr.photos.getSizes`
- `flickr.photos.getExif`
- `flickr.people.getInfo`
- `flickr.people.getPhotos`
- `flickr.photosets.getList`
- `flickr.photosets.getPhotos`
- `flickr.favorites.getList`
- `flickr.groups.pools.getPhotos`
- `flickr.tags.getHotList`
- `flickr.places.*`

Keep raw fallback support intact while this roadmap advances.
