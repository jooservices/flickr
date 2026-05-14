# CI/CD

GitHub Actions runs on `master` and `develop` pushes and pull requests. Normal CI sets `FLICKR_REAL_TESTS=false`; real Flickr credentials must never be required for pull request validation.

The PR gate is:

```bash
composer validate --strict
composer install --prefer-dist --no-interaction --no-progress
composer check
```

`composer check` runs `composer lint:all` and `composer test`. Coverage remains available through `composer ci`, but it requires a working local or CI coverage driver and should not make normal PR validation fragile.
