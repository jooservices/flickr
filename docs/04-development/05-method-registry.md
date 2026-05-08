# Method Registry Maintenance

The SDK tracks the official Flickr REST method index in two deterministic files:

- `tests/Fixtures/official-flickr-methods.php`
- `src/Metadata/methods.php`

Normal CI must not scrape Flickr. Keep verification local and repeatable:

```bash
php tools/verify-method-registry.php
vendor/bin/phpunit --filter OfficialMethodCoverageTest
```

When Flickr adds or changes a method:

1. Check the official method page at `https://www.flickr.com/services/api/`.
2. Update `tests/Fixtures/official-flickr-methods.php`.
3. Update `src/Metadata/methods.php` with docs URL, HTTP method, auth requirement, OAuth permission, and cache policy.
4. Add or update the matching service wrapper.
5. Add a DTO or mapper only when the workflow benefits from typed data.
6. Add tests for the registry entry and wrapper behavior.
7. Keep raw fallback available for unknown future methods.

Upload and replace are intentionally outside normal REST generation because Flickr uses a binary upload endpoint.
