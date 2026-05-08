# Full API Index

The package exposes service wrappers for all 224 methods currently listed in Flickr's official API method index.

Wrapper naming follows Flickr namespaces:

- `flickr.activity.*` -> `activity()`
- `flickr.auth.*` -> `authApi()`
- `flickr.auth.oauth.*` -> `authOauthApi()`
- `flickr.blogs.*` -> `blogs()`
- `flickr.cameras.*` -> `cameras()`
- `flickr.collections.*` -> `collections()`
- `flickr.commons.*` -> `commons()`
- `flickr.contacts.*` -> `contacts()`
- `flickr.favorites.*` -> `favorites()`
- `flickr.galleries.*` -> `galleries()`
- `flickr.groups.*` -> `groups()`
- `flickr.groups.members.*` -> `groupsMembers()`
- `flickr.groups.pools.*` -> `groupsPools()`
- `flickr.groups.discuss.topics.*` -> `groupsDiscussTopics()`
- `flickr.groups.discuss.replies.*` -> `groupsDiscussReplies()`
- `flickr.interestingness.*` -> `interestingness()`
- `flickr.machinetags.*` -> `machinetags()`
- `flickr.panda.*` -> `panda()`
- `flickr.people.*` -> `people()`
- `flickr.photos.*` -> `photos()`
- `flickr.photos.comments.*` -> `photosComments()`
- `flickr.photos.geo.*` -> `photosGeo()`
- `flickr.photos.licenses.*` -> `photosLicenses()`
- `flickr.photos.notes.*` -> `photosNotes()`
- `flickr.photos.people.*` -> `photosPeople()`
- `flickr.photos.suggestions.*` -> `photosSuggestions()`
- `flickr.photos.transform.*` -> `photosTransform()`
- `flickr.photos.upload.*` -> `photosUpload()`
- `flickr.photosets.*` -> `photosets()`
- `flickr.photosets.comments.*` -> `photosetsComments()`
- `flickr.places.*` -> `places()`
- `flickr.prefs.*` -> `prefs()`
- `flickr.profile.*` -> `profile()`
- `flickr.push.*` -> `push()`
- `flickr.reflection.*` -> `reflection()`
- `flickr.stats.*` -> `stats()`
- `flickr.tags.*` -> `tags()`
- `flickr.test.*` -> `test()`
- `flickr.testimonials.*` -> `testimonials()`
- `flickr.urls.*` -> `urls()`

Most complete-index wrappers use this shape:

```php
$response = $flickr->places()->find([
    'query' => 'Ho Chi Minh City',
]);
```

DTO-first wrappers remain available for selected high-use workflows such as `photos()->search(...)`, `photosets()->create(...)`, and `uploads()->upload(...)`.
