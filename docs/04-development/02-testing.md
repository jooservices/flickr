# Testing

Normal CI tests must not call Flickr. Real tests are skipped unless `FLICKR_REAL_TESTS=true` and credentials are present.

Run focused tests while editing, then run the full local gate before claiming completion:

```bash
composer lint:all
composer test
composer check
php tools/verify-method-registry.php
```

Registry, OAuth signing, token storage, cache metadata, upload, replace, parser, and file handling changes need focused edge-case tests. Do not commit real access tokens, API secrets, user media, or fixtures copied from private Flickr responses.
