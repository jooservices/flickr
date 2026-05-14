# Method Registry Maintenance

The SDK tracks the official Flickr REST method index in two deterministic files:

- `tests/Fixtures/official-flickr-methods.php`
- `src/Metadata/methods.php`

Last verified against the official Flickr REST API index on 2026-05-14.

Source URLs:

- REST API index: https://www.flickr.com/services/api/
- Upload API: https://www.flickr.com/services/api/upload.api.html
- Replace API: https://www.flickr.com/services/api/replace.api.html

Upload and replace are separate binary workflows and are not counted as REST methods. `flickr.photos.upload.checkTickets` is a REST method and remains in the 224-method registry.

Normal CI must not scrape Flickr. Keep verification local and repeatable:

```bash
php tools/verify-method-registry.php
vendor/bin/phpunit --filter OfficialMethodCoverageTest
```

When Flickr adds or changes a method:

1. Check the official method page at `https://www.flickr.com/services/api/`.
2. Regenerate or update `tests/Fixtures/official-flickr-methods.php` from REST method links only.
3. Update `src/Metadata/methods.php` with docs URL, HTTP method, auth requirement, OAuth permission, and cache policy.
4. Add or update the matching service wrapper.
5. Add a DTO or mapper only when the workflow benefits from typed data.
6. Add tests for the registry entry and wrapper behavior.
7. Keep raw fallback available for unknown future methods.

One local way to compare the live index against the fixture is:

```bash
php -r '$html=file_get_contents("https://www.flickr.com/services/api/"); preg_match_all("~href=\"/services/api/(flickr\\.[A-Za-z0-9_.]+)\\.html\"~", $html, $m); $live=array_values(array_unique($m[1])); $fixture=require "tests/Fixtures/official-flickr-methods.php"; echo "live=".count($live).PHP_EOL; echo "missing local: ".implode(",", array_diff($live, $fixture)).PHP_EOL; echo "extra local: ".implode(",", array_diff($fixture, $live)).PHP_EOL;'
```

Then run:

```bash
php tools/verify-method-registry.php
vendor/bin/phpunit --filter OfficialMethodCoverageTest
```

Cache policy must be conservative. Auth methods, OAuth methods, upload ticket checks, authenticated calls, and mutation/write/delete methods must not be cacheable by default. This includes methods whose name starts with `add`, `edit`, `delete`, `remove`, `set`, `create`, `join`, `leave`, `post`, `approve`, `reject`, `subscribe`, `unsubscribe`, `rotate`, `correctLocation`, or `batchCorrectLocation`, plus any POST or permissioned method.
