# jooservices/flickr

This file adds project-only rules.

- PHP `^8.5`; runtime: `jooservices/client ^4.0`, `jooservices/dto ^3.0`, Nyholm PSR-7
- First public line: **`v4.0.0`**; current line: **`v4.1.0`** — no backward compatibility with v2
- All PHP tooling via Docker (`php:8.5-cli-bookworm`, image `jooservices/flickr:php85`)
- CI on GitHub-hosted `ubuntu-latest` via `tools/ci/docker-compose`
- Lints at **max** with **no ignore**: Pint `per`, PHPCS, PHPStan max + strict rules, PHPMD, PHP-CS-Fixer
- Coverage floor **85%** (`tools/coverage-enforce.php`); Arch + Unit + Integration suites
- Generated surface (`src/Flickr.php`, most `src/Services/*Api.php`, `docs/api-index.md`) comes from manifests — never hand-edit
- Branch model: `develop` for integration, `master` for production, tags from `master`
