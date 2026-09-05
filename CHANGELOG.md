# Changelog

All notable changes are documented here. Format follows
[Keep a Changelog](https://keepachangelog.com/) with Conventional-Commit tags.

## [Unreleased]

### Removed

- Drop shipped rebuild planning docs (`knowledge.md`, `plan.md`, `implementation.md`) and the temporary `docs/audit-remediation.md` note; contributors use README and AGENTS.md.

### Fixed

- Preserve multiline upload fields exactly as signed and rewind multipart string conversion; close/detach now expose the correct stream state.
- Retain configured credentials during redactor eviction, refresh active secrets, and redact OAuth verifiers and JSON/XML error messages.
- Reject invalid UTF-8 cache-key input instead of hashing failed serialization.
- Reject failed OAuth HTTP responses before trusting token fields; preserve rate-limit retry metadata.
- Surface ticket API failures and avoid sleeping after a request consumes the poll deadline.
- Verify all generated API files without silently overwriting drift.
- Correct README client imports and constrain supported PHP versions to `^8.5`.

### Changed

- Sonar quality-gate completion is required in CI, including bot changes.
- PR commit checks enforce author/committer identity and validate merge subjects.
- Add Faker-backed security regressions and generator drift tests; remove the two PHPStan argument ignores by accurately typing validated input boundaries.

## [4.1.0] - 2026-08-28

### Fixed
- Upload/replace XML parsing now reads official Flickr `photoid`/`ticketid` text nodes (with attribute fallbacks) instead of the invented `<photo id>` / `<ticketid id>` shapes.
- Ticket poller reads official `uploader.ticket` JSON with `photoid` and integer or string `complete` codes (`0`/`1`/`2`); legacy `tickets.ticket` / `photo_id` still accepted.
- `ApiResponseData::listAt()` wraps a single associative object as a one-element list — Flickr often returns one item as an object, not an array.
- `photos.getInfo` hydrates `photo.dates.posted` / `photo.dates.taken` (with `dateposted`/`date_posted`/`datetaken` fallbacks).
- Typed photo workflows force `throwOnApiError` so `stat=fail` raises instead of empty DTOs.
- `PhotosApi::search()` and `getRecent()` accept `ApiCallOptions` so private-library searches can sign.
- `PhotoUrlBuilder` uses `https://live.staticflickr.com/{server}/{id}_{secret}{suffix}.jpg`.
- `SearchPhotosData` default `perPage` is 30 (Flickr app TOS); max remains 500.
- README OAuth callback example uses `token:` instead of the invalid `oauthToken:` named argument.

## [4.0.0] - 2026-08-26

Complete rebuild. **No backward compatibility** with the archived v2 line.

### Added
- Framework-agnostic Flickr SDK on `jooservices/client:^4.0` + `jooservices/dto:^3.0` (PHP >=8.5).
- Frozen 224-method registry (`resources/method-registry.php`) with provenance hash, generated explicit service wrappers, facade accessors and docs index — parity enforced by tests and CI.
- Public universal gateway `Api::call()` / `Api::raw()` (explicit verb + auth mode, never cached) / `Api::describe()`.
- OAuth 1.0a stateless `begin()/complete()` transaction with immutable `PendingAuthorization`, 10-minute expiry, `hash_equals` callback binding, HMAC-SHA1 signer isolated to Flickr.
- Multipart upload/replace over PSR-7 streams with file validation, quoted multi-word tags, typed sync/async outcomes, XML hardening (`LIBXML_NONET`, DOCTYPE/entity rejection), safe error metadata (`non_pro_desktop_upload_wait_time`, duplicates) and no auto-retry.
- Bounded ticket poller with injected clock/sleeper and terminal states.
- PSR-16 response caching for eligible public GETs only; deterministic SHA-256 keys; absolute bypass for auth/mutation/upload/tickets.
- Lazy bounded paginator, priority typed DTOs/hydrators (`photos.search/getInfo/getSizes/getExif/getRecent`), `PhotoUrlBuilder`.
- `Testing\FlickrFake` built on client v4 fake registry with ordered semantic assertions and offline consumer smoke.
- Docker-only quality toolchain: Pint, PHPCS, PHPStan max+strict (0 errors), PHPMD, PHP-CS-Fixer, PHPUnit strict suites with 85% coverage gate, registry/index verifiers, consumer smoke.
- GitHub Actions suite ported from org conventions (CI, post-merge, CodeQL, workflow audit, Scorecard, commitlint, semantic PR, labeler, stale, link-check, release gate).

### Changed
- Retry/resilience/TLS/timeout policy moved entirely to client v4 configuration; SDK adds no retry middleware and disables redirects per request.

### Removed
- Legacy `flickr.auth.*` / `flickr.auth.oauth.*` live support and Panda support (metadata-only, fail before network).
- General XML REST parsing, Guzzle option bags, `parse_str()`, magic `__call`, upload-limit enforcement via `people.getLimits`, force-cached ticket polling.

[Unreleased]: https://github.com/jooservices/flickr/compare/v4.1.0...HEAD
[4.1.0]: https://github.com/jooservices/flickr/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/jooservices/flickr/releases/tag/v4.0.0
