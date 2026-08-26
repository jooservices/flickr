# Flickr SDK v4.0.0 — Rebuild Plan

> Objective: a clean PHP 8.5+ Flickr SDK, no backward compatibility, on `client:^4.0` and `dto:^3.0`; complete 224-method coverage; full green release gate; >=85% coverage. `knowledge.md` is the design authority.

## Fixed constraints

1. Port archived Flickr behaviour, **not** its v2 client/DTO contracts.
2. Client v4 owns PSR HTTP, transport, TLS, timeout, redirect implementation, retry/resilience and fakes. Flickr owns Flickr protocol semantics and sends the portable per-request security delta `allowRedirects: false`; it never installs retry middleware or permits POST mutation/upload replay.
3. DTO v3 owns public data mapping. No Guzzle/framework/exceptions package dependency.
4. SOLID, DRY, KISS and YAGNI are enforced by architecture tests/review; approved patterns are Facade, Factory, Adapter, Registry, Strategy, Builder, Null Object and Generator only.
5. All code follows the PHP 8.5 standard in `knowledge.md`; no compatibility code or magic dispatch.
6. GitHub Actions must use the current client/DTO quality/release model and must pass Actionlint/Zizmor.

## Phases

### P0 — Repository and quality foundation

- [ ] Add Composer runtime/dev dependencies, PSR-4, MIT license, README/CHANGELOG/CONTRIBUTING/SECURITY, Docker PHP 8.5 tools, Make targets and safe CaptainHook setup.
- [ ] Configure strict PHPUnit Arch/Unit/Integration suites, 85% Clover enforcement/merge, Pint, PHPCS, PHPStan max+strict, PHPMD and PHP-CS-Fixer.
- [ ] Add CI, post-merge, CodeQL, Actionlint/Zizmor workflow audit, Scorecard, commitlint, semantic PR, labeler, stale, link-check, registry-drift and tag-release workflows.
- [ ] Pin Actions/container references immutably, configure least privilege, Docker vendor cache and branch/release policy documentation.

**Exit:** clean Composer install; `composer validate --strict`; all empty-tooling/workflow validation passes.

### P1 — Core domain + client v4 compatibility spike

- [ ] Add enums, config, package-owned immutable `FlickrEndpoints`, raw/API/error/request/pagination/method DTOs, exception hierarchy and secret redactor.
- [ ] Add narrow cache/token/signer/transport contracts plus null implementations.
- [ ] Build `ClientV4Transport` using `HttpClient::sendRequest()`/`send()` and client v4 request builder; map PSR response to raw response safely.
- [ ] Build `FlickrFactory` and facade with injected client/cache/token store; no concrete curl construction or configurable endpoint host/path.
- [ ] Test exact trusted endpoints, public GET, POST form, portable request options, fake path, retry-safety boundary and redacted network failure.

**Exit:** one fake `photos.search` reaches client v4 without old interfaces or Guzzle option bags.

### P2 — Registry and raw pipeline

- [ ] Create reviewed `resources/method-registry.php` and `resources/api-surface.php`: exact 224 records, provenance URL/retrieval date/normalized-name SHA-256, service/accessor mapping and availability metadata.
- [ ] Implement deterministic registry/wrapper/docs generators and offline verification. Add weekly/manual drift detection that uploads a diff artifact but never auto-commits, auto-updates metadata or releases.
- [ ] Implement parameter/list/tag normalizer, RFC-3986 query/base-string builders and deterministic OAuth1 HMAC-SHA1 signer.
- [ ] Implement REST request builder, JSON parser, upload-only XML parser and error mapper.
- [ ] Implement public `Api` gateway (`call`, explicit `raw`, `describe`) over internal `ApiClient`; `ApiClient` performs registry → normalize → cache → auth/sign → build → client adapter → parse/error → cache write.
- [ ] Implement `AuthenticationMode` and separate `RawCallOptions`: known calls obey registry/explicit mode; raw permits only GET/POST, requires explicit authenticated/unauthenticated mode and is always non-cacheable.
- [ ] Add raw fallback and intentional unavailable-legacy method handling.

**Exit:** fixed OAuth vectors, endpoint/signature and raw-auth safety tests, JSON/XML/error/cache tests pass; exact 224 records plus frozen-manifest provenance verify.

### P3 — All explicit service wrappers

- [ ] Implement common service helper over public `Api` and every domain/accessor in the frozen API-surface manifest; all wrappers must use that one API gateway.
- [ ] Generate explicit wrappers, facade accessors and API index from the reviewed manifests; no `__call`, archive accessor aliases or hidden name conversion.
- [ ] Test every wrapper/accessor maps to the correct registry method and metadata, including unavailable legacy/Panda methods failing before send.

**Exit:** 224 registry/wrapper/docs-index parity is enforced in test and CI.

### P4 — Typed workflow DTOs and hydrators

- [ ] Rebuild DTO v3 models/hydrators for photos/search/info/sizes/exif/recent, people, favorites, galleries, photosets, groups, tags, places and upload tickets/results.
- [ ] Add typed helper methods and `PhotoUrlBuilder`; preserve generic `ApiResponseData` for all untyped wrappers. Validate `photos.search` `perPage` 1..500 and `page >= 1`; document Flickr's 4,000-result query cap.
- [ ] Store sanitized real-shaped fixtures and test mapping/normalization/null/enums/nested collections.

**Exit:** every typed helper has a fixture, DTO, hydrator and contract test; no unsupported automatic DTO sweep.

### P5 — OAuth and token stores

- [ ] Implement stateless `begin()`/`complete()` OAuth flow: immutable `PendingAuthorization`, callback DTO, 10-minute expiry, `oauth_callback_confirmed`, constant-time callback-token binding and mandatory InMemory/Null access-token stores. Never retain request-token secrets in mutable SDK state.
- [ ] Port File/Encrypted stores only after complete permissions/corruption/key/tamper/redaction test suite passes; otherwise omit them intentionally.
- [ ] Enforce token/permission before send and prove no secret can surface in errors/logs/fixtures/debug output. Document consumer-owned, server-side pending-authorization storage and user/session binding.

**Exit:** OAuth vectors and token-store test matrix fully green.

### P6 — Upload, tickets, cache and pagination

- [ ] Implement multipart upload/replace with PSR streams, robust file validation, write-token check, typed async input/XML outcomes/error-code metadata and resource closure.
- [ ] Implement bounded ticket poller with injectable clock/sleeper. Use authenticated `people.getUploadStatus` only as advisory quota/status; never use archive `getLimits` as an upload gate and never cache ticket polling.
- [ ] Implement PSR-16/null cache and deterministic keys for parsed public metadata only; add lazy paginator and service page helpers. No photo binary download/cache capability.

**Exit:** no-network multipart/cache/pagination/ticket terminal-state suite is green.

### P7 — Consumer kit, docs and hardening

- [ ] Build public `Testing\FlickrFake` on client v4 fake registry; reset state reliably.
- [ ] Implement a clean Composer consumer smoke test using fake public search, typed/raw call, OAuth URL and upload.
- [ ] Document install, package-owned endpoints, client-v4 safe-retry boundary, APIs, raw explicit-auth fallback, OAuth pending transaction/token persistence, errors, cache, upload/tickets, fake, security, Flickr API terms/attribution/compliance and v4 no-BC policy.
- [ ] Run lint/audit/security scans and raise honest branch coverage to >=85%; include endpoint tampering, OAuth transaction/replay, raw-cache/auth, mutation-retry and terms-facing documentation assertions in the release checklist.

**Exit:** consumer smoke, docs examples, all local `composer ci` checks and coverage gate pass.

### P8 — Remote release and publication

- [ ] Merge scoped green PRs into `develop`; verify required checks/branch protection remotely.
- [ ] Create `release/4.0.0` from develop with metadata-only changes; green PR to master.
- [ ] Rehearse the complete tag gate: validate/lint/test coverage, registry/index, smoke, Composer audit, OSV, Gitleaks, Semgrep, CodeQL, Actionlint, Zizmor, Trivy and SBOM.
- [ ] Verify public Composer resolution; tag annotated `v4.0.0` only from green master; monitor GitHub Release, SBOM and Packagist; merge master back to develop.

**Exit:** every item in `knowledge.md` Release Definition of Done is evidenced remotely. A red/skipped required check blocks tagging and publishing.

## Required Composer commands

```text
composer validate
composer lint:pint
composer lint:phpcs
composer lint:phpstan
composer lint:phpmd
composer lint:cs
composer lint
composer test
composer test:coverage
composer coverage:check
composer verify:registry
composer verify:api-surface
composer generate:api-index
composer verify:api-index
composer verify:smoke
composer audit
composer check
composer ci
```

`composer ci` is the credentials-free local equivalent of the PR gate. Real Flickr tests require explicit `FLICKR_REAL_TESTS` plus all credentials and never run destructive operations without explicit approval.
