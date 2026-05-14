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

### Changed

- Composer lint scripts now run php-cs-fixer alongside Pint, PHPCS, PHPStan, and PHPMD.
- Documentation index and root README now better describe SDK identity, testing, release readiness, and non-Laravel scope.

### Security

- Examples read credentials from environment variables and avoid embedded secrets.
