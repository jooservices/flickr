# JOOservices Flickr AI Contributor Guide

## Package Identity

`jooservices/flickr` is a pure PHP 8.5+ SDK for Flickr. It is framework-agnostic and is not a Laravel package.

Do not add a service provider, facade, route, migration, config publishing, Artisan command, or Laravel-only integration code here. Laravel support belongs in a separate package.

## Non-Negotiable Rules

- Inspect the actual source before non-trivial changes.
- Do not assume behavior. If a requirement is unclear or conflicts with the repository, stop and ask.
- Official Flickr API docs are the source of truth: https://www.flickr.com/services/api/
- Follow `jooservices/dto` style for DTO and Data objects.
- Use `jooservices/client` for HTTP transport.
- Keep raw API fallback working for unknown or future Flickr REST methods.
- Keep upload and replace separate from normal REST calls; Flickr binary upload uses `up.flickr.com`.
- Pint wins when formatter rules conflict.
- Prefer enums or constants for domain values.
- Do not expose public associative arrays where a DTO is the better public API. Raw fallback may accept arrays by design.

## Architecture Map

User code builds the SDK through `FlickrFactory`, then calls `Flickr` service accessors. Services translate friendly methods into raw Flickr method calls. DTOs represent user-facing inputs and mapped responses where workflows benefit from typed objects. The method registry stores Flickr method metadata, including docs URL, auth requirement, OAuth permission, HTTP method, and cache policy.

HTTP calls flow through `FlickrClient` for REST API methods and `FlickrUploadClient` for upload and replace. OAuth signing and token storage live under `src/Auth`. `JooClientTransport` adapts this package to `jooservices/client`, then requests reach Flickr.

Upload/replace flow is intentionally separate:

`User code -> FlickrFactory -> Flickr -> UploadService -> FlickrUploadClient -> FlickrTransportContract -> Flickr upload endpoint`

REST flow:

`User code -> FlickrFactory -> Flickr -> Service -> RawApiService -> FlickrClient -> FlickrTransportContract -> jooservices/client -> Flickr API`

## Source Directories

- `src/Auth`: OAuth signer, authenticator, token stores.
- `src/Cache`: cache adapters and cache key support.
- `src/Client`: REST client, upload client, transport adapter, parser, request helpers.
- `src/Config`: SDK configuration.
- `src/Contracts`: public contracts for auth, cache, client, and services.
- `src/DTO`: DTO-first public data objects.
- `src/Enums`: Flickr domain values.
- `src/Exceptions`: SDK exception hierarchy.
- `src/Mappers`: response mapping helpers.
- `src/Metadata`: official method registry and definitions.
- `src/Services`: Flickr service wrappers and raw fallback.
- `src/Support`: normalization, signing helpers, URL and file support.
- `tests`: unit, integration, fixtures, and fakes.
- `docs`: architecture, getting started, user guide, examples, development, maintenance.
- `examples`: manually runnable scripts.

## Adding A Flickr Method Wrapper

1. Check the official Flickr method docs first.
2. Update `src/Metadata/methods.php` with docs URL, HTTP method, auth permission, and cache policy.
3. Add or update the appropriate service wrapper.
4. Add a DTO only when the method has a complex or friendly workflow.
5. Add a mapper when the response is important enough to expose as typed data.
6. Add unit tests for happy, unhappy, weird, edge, and security-sensitive behavior where relevant.
7. Add docs or an example for user-facing workflows.
8. Keep unknown-method raw fallback working.

## Testing Policy

- Normal tests must not call the real Flickr API.
- Real Flickr integration tests must stay opt-in behind `FLICKR_REAL_TESTS=true`.
- Cover happy, unhappy, weird, edge, invalid, security, and exploit-style cases where relevant.
- Token store, file handling, upload, OAuth signing, parser, and registry changes need focused edge tests.
- Do not commit secrets, real access tokens, or snapshots containing credentials.

## Performance Rules

- Cache safety comes before cache features.
- Never cache auth, OAuth, mutation, private authenticated, upload, replace, or upload ticket workflows.
- Optional REST caching must stay limited to public cacheable GET calls and must be disabled by default unless a cache adapter is passed.
- Do not add concurrency unless the current transport architecture clearly supports it and tests prove the behavior.
- Prefer lazy pagination helpers over loading all pages into memory.
- Upload and replace should stream files where possible and close file handles after transport handoff.
- Benchmark only when it would produce meaningful evidence for a real performance decision.

## Git Flow

- Normal implementation work branches from the latest `develop`.
- Open feature and fix PRs back into `develop`.
- Release branches start from `develop` as `release/<version>`.
- Release PRs target `master`; tags and GitHub releases are created from `master`.
- After release or hotfix merges to `master`, merge `master` back into `develop`.
- Do not commit directly to `master` or `develop`.
- Do not delete unmerged branches silently.
- Never delete protected branches: `master` and `develop`.

## Quality Commands

```bash
composer install
composer lint:all
composer test
composer check
composer ci
```

Run targeted tests for focused changes, then run the full checks before claiming completion.

## Final Report Expectations

Report changed files, what changed, why it changed, how it was validated, exact commands run, failures, and known gaps. Do not claim completion if tests or lints fail.
