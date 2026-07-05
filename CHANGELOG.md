# Changelog

All notable changes to `jooservices/flickr` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## v1.1.1 - 2026-07-06

### Changed

- Bumped dev dependencies: `phpunit/phpunit` to `^13.0`, `squizlabs/php_codesniffer` to `^4.0`.
- Bumped GitHub Actions: `actions/upload-artifact` to `7.0.1`, `actions/labeler` to `6.1.0`, `amannn/action-semantic-pull-request` to `6.1.1`, `shivammathur/setup-php` to `2.37.2`, `softprops/action-gh-release` to `3.0.1`.

## v1.1.0 - 2026-07-06

### Added

- Response hydrators and typed `*Data()` helpers for priority Flickr methods (photos, people, photosets, favorites, groups, tags, places).
- `PhotoUrlBuilder`, `Paginator`, `EncryptedTokenStore`, and search enums (`License`, `PrivacyFilter`, `SortOrder`, `PhotoExtra`, `PhotoSize`).
- `PlaceData` DTO and generated full API index at `docs/02-user-guide/12-full-api-index.md`.
- Repository standards aligned with `jooservices/dto`: release/scorecard/semantic-pr/secret-scanning/pr-labeler workflows, PR/issue templates, CONTRIBUTING, CODE_OF_CONDUCT, `.editorconfig`, `.gitattributes`, `.gitleaks.toml`.
- CI coverage gate (95%), Codecov upload, `verify:registry`, and `verify:api-index` composer scripts.
- Dependabot configuration and SECURITY.md.

### Changed

- README and AGENTS.md now accurately describe typed request DTOs and priority response helpers instead of blanket "DTO-first responses".
- `SearchPhotosData` `extraParameters` now win over canonical defaults; `CachePolicy::Enabled` honors opt-in caching.
- `PhotoService::searchPages()`, `PeopleService::getPhotosPages()`, and `FavoriteService::getListPages()` delegate to generic `Paginator`.
- `jooservices/client` bumped to `^1.4` with `FlickrConfig::$retryTimes` wired to transport retries.
- `jooservices/dto` bumped to `^1.2`.
- OAuth request-token flow defaults `oauth_callback` to `oob` when no callback URL is configured.

### Fixed

- Transport exception messages redact signed URL credentials.
- `FileTokenStore` wraps invalid token shapes as `TokenStorageException`; token files use `chmod 0600`.
- Upload tag quoting for multi-word tags, `fopen()` guard in multipart builder, XML fail payload preservation, case-insensitive `Retry-After` parsing.
- HTTP status included in malformed JSON error messages.

### Deprecated

- `PhotosUploadService::checkTickets()` — use `UploadService::checkTickets()` instead.
- Legacy/discontinued Flickr methods (`flickr.auth.*`, `flickr.panda.*`, `flickr.push.*`) flagged in method registry.

### Removed

- Empty `FlickrUploadRequestBuilder` class and unused auth DTOs (`AuthorizationUrlData`, `OAuthConsumerData`, `AuthorizedUserData`).
- Deleted placeholder `src/Mappers/` layer (replaced by `src/Hydrators/`).

### Security

- `SensitiveDataRedactor` strips query strings and OAuth parameters from transport error messages.
- Optional `EncryptedTokenStore` decorator for libsodium-encrypted token persistence.

## v1.0.0 - 2026-05-14

### Added

- GitHub Actions CI workflow for Composer validation, linting, and tests.
- Public `FakeFlickrTransport` for SDK consumer tests without real network calls.
- Method registry verification tooling and maintenance docs.
- Runnable examples for raw REST, search, photo info, OAuth, upload, replace, async tickets, mock transport, and custom cache usage.
- Release readiness and repository metadata documentation.
- Full registry and service-wrapper coverage for 224 official Flickr REST API methods.
- OAuth 1.0a authentication, token storage, raw REST calls, upload, replace, and async ticket polling workflows.
- DTO-first helpers for high-value photo, people, photoset, upload, replace, and response workflows.
- Optional public GET caching through package cache contracts and PSR-16 adapter support.
- Lazy `photos()->searchPages()` pagination helper.
- AI contributor workflow, CI/CD, secret-handling, method-registry, and repository metadata docs.

### Changed

- Composer lint scripts now run php-cs-fixer alongside Pint, PHPCS, PHPStan, and PHPMD.
- Documentation index and root README now better describe SDK identity, testing, release readiness, and non-Laravel scope.
- Cache metadata is conservative for auth, OAuth, upload ticket, authenticated, permissioned, and mutation methods.
- Upload and replace multipart requests now close file stream handles after transport requests complete.
- Test coverage was raised above the 95% statement coverage release target.

### Security

- Examples read credentials from environment variables and avoid embedded secrets.
- Normal test and CI paths keep real Flickr API calls opt-in behind `FLICKR_REAL_TESTS=true`.
