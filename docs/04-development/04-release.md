# Release

Use this checklist before tagging a release.

Normal implementation branches start from `develop` and open PRs back to `develop`. Release branches start from `develop` as `release/<version>` and open PRs to `master`. Tag and publish releases from `master`, then merge `master` back into `develop`.

## Preflight

```bash
git status --short
composer validate --strict
composer install
composer lint:fix
composer lint:all
composer verify:registry
composer verify:api-index
composer test
composer check
composer ci
```

Real Flickr tests are opt-in only:

```bash
FLICKR_REAL_TESTS=true composer test -- --filter RealFlickrTest
```

## Review

- Confirm no secrets, OAuth tokens, or real credentials are committed.
- Confirm examples read credentials from environment variables.
- Confirm upload and replace examples require explicit file paths.
- Confirm README, `AGENTS.md`, `CHANGELOG.md`, and docs match the public API.
- Confirm `docs/02-user-guide/12-full-api-index.md` is current (`composer generate:api-index` if needed).
- Confirm `LICENSE.md` matches the Composer license.

## Versioning

Use semantic versioning:

- Patch: bug fixes and docs-only polish.
- Minor: backward-compatible features or new Flickr method helpers.
- Major: breaking public API, DTO shape, namespace, or behavior changes.

## Tagging

Pushing a `v*.*.*` tag to `master` triggers `.github/workflows/release.yml`, which:

1. Runs validation (`composer lint:all`, `composer verify:registry`, `composer verify:api-index`, `composer test`)
2. Creates a GitHub release with generated notes
3. Triggers Packagist update when `PACKAGIST_USERNAME` and `PACKAGIST_TOKEN` are configured

Manual fallback:

```bash
git checkout master
git pull origin master
git tag vX.Y.Z
git push origin vX.Y.Z
```

Paste the relevant `CHANGELOG.md` section into the GitHub release if auto-generated notes are insufficient.

## Packagist

Packagist update is automated by `release.yml` when secrets are configured. Verify the new version appears on Packagist after the workflow completes.
