# Changelog

All notable changes are documented here. Format follows
[Keep a Changelog](https://keepachangelog.com/) with Conventional-Commit tags.

## [4.0.0] — unreleased

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
