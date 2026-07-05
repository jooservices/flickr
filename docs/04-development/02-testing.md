# Testing

Normal CI tests must not call Flickr. Real tests are skipped unless `FLICKR_REAL_TESTS=true` and credentials are present.

Run focused tests while editing, then run the full local gate before claiming completion:

```bash
composer lint:all
composer verify:registry
composer verify:api-index
composer test
composer check
composer ci
```

Registry, OAuth signing, token storage, cache metadata, upload, replace, parser, hydrators, and file handling changes need focused edge-case tests. Do not commit real access tokens, API secrets, user media, or fixtures copied from private Flickr responses.

Coverage must stay at or above **95%** statement coverage before release (`composer ci`).
