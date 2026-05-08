# Release

Use this checklist before tagging a release.

## Preflight

```bash
git status --short
composer validate --strict
composer install
composer lint:fix
composer lint:all
composer test
composer check
```

Run coverage when the local environment has a working coverage driver:

```bash
composer test:coverage
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
- Confirm README and docs match the public API.
- Confirm `CHANGELOG.md` has the release notes.
- Confirm `LICENSE.md` matches the Composer license.

## Versioning

Use semantic versioning:

- Patch: bug fixes and docs-only polish.
- Minor: backward-compatible features or new Flickr method helpers.
- Major: breaking public API, DTO shape, namespace, or behavior changes.

## Tagging

Do not create tags or GitHub releases from automation unless explicitly authorized.

Manual release flow:

```bash
git tag vX.Y.Z
git push origin vX.Y.Z
```

Create the GitHub release from the tag and paste the relevant `CHANGELOG.md` section.

## Packagist

Do not publish to Packagist from CI. After the GitHub release, verify Packagist is synced or trigger a manual update from Packagist if needed.
