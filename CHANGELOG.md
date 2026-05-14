# Changelog

All notable changes to `jooservices/flickr` will be documented in this file.

This project follows semantic versioning where practical.

## Unreleased

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
