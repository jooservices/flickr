# Flickr SDK v4.0.0 — Knowledge Base

> Approved rebuild baseline, audited 2026-08-25. This is a new major: no compatibility with Flickr v2, client v2, or DTO v1.

## 1. Product decision and verified evidence

- Package: `jooservices/flickr` **v4.0.0**, PHP >=8.5, framework-agnostic SDK only.
- Runtime: `jooservices/client:^4.0`, `jooservices/dto:^3.0`, `psr/simple-cache:^3.0`. Never depend on Guzzle, a framework, or `jooservices/exceptions`.
- Archive source of behaviour: `/Users/vietvu/Sites/archives/JOOservices.2/flickr`: 195 source files, 224 registry methods, 43 services, 34 DTOs, 9 hydrators and 17 test classes. Old lock: client 2.3.0, DTO 1.5.1, exceptions 0.5.0; do not reuse these contracts.
- Current foundations: client **v4.0.0** (163 tests passed) and DTO **v3.0.0** (685 tests passed). Client v4 depends on DTO ^3, ships final `HttpClient` implementing PSR-18 `ClientInterface` (plus `send(Request, RequestOptions)`), immutable `ClientBuilder`, PSR-7 request builder, portable request options, hardened transports/middleware and deterministic fakes.
- Flickr official docs remain authoritative: REST is HTTP GET/POST, OAuth 1.0a requires HMAC-SHA1, and upload/replace use their own HTTPS endpoints. Client v4's HMAC-SHA256 signer does **not** replace Flickr OAuth.
- Official API terms are a product constraint, not an optional README footnote: applications must respect photo-owner restrictions, attribution/disclaimer, removal/privacy requests, the 30-photo-per-page limit, and reasonable photo-cache periods. The SDK must never download or cache photo binaries.

## 2. Scope and non-goals

| In v4.0.0 | Explicitly excluded |
| --- | --- |
| All 224 archived REST registry methods and explicit service wrappers | Any BC shim, aliases or upgrade bridge |
| Raw fallback, registry discovery, OAuth, upload/replace/tickets, public cache, paginator, deterministic fake | Laravel/framework integration, CLI, queues, background worker, event bus |
| Priority typed DTOs/hydrators; generic `array<string,mixed>` + package-owned envelope DTOs (`ApiResponseData`, `ApiErrorData`) built on dto v3 — dto v3 itself does not ship them | Direct Guzzle usage and duplicate HTTP/resilience implementation |
| JSON REST and XML only for upload/replace responses | General XML REST hydration, concurrency API, screen scraping |

Legacy `flickr.auth.*` / `flickr.auth.oauth.*` entries requiring obsolete `api_sig`, and discontinued Panda methods, remain visible in registry/docs but are marked unavailable/deprecated and fail clearly. They are never falsely advertised as OAuth support.

## 3. Strict architecture

```text
Consumer ClientBuilder v4: transport, TLS, timeout, retry, redirect implementation, resilience, fakes
Consumer FlickrFactory v4: FlickrConfig + injected HttpClient + cache + token store

Flickr facade → Api (public universal gateway) → ApiClient (internal pipeline)
              └→ explicit domain services ───────┘
                                                ├─ MethodRegistry
                                                ├─ OAuth1Signer (local HMAC-SHA1)
                                                ├─ ClientV4Transport adapter → HttpClient v4
                                                ├─ ResponseParser / Flickr errors
                                                └─ Cache policy
UploadService → MultipartRequestBuilder → ClientV4Transport
DTO v3 ← small static hydrators ← Flickr JSON/XML fixtures
```

### Responsibilities

| Component | Owns | Must not own |
| --- | --- | --- |
| `FlickrFactory` / `Flickr` | Explicit composition and service accessors | Global state, HTTP configuration |
| `FlickrEndpoints` | Immutable, package-owned Flickr endpoint URIs | User-configurable host/path or test-only endpoint switching |
| `Api` | Public `call()`, `raw()`, `describe()` gateway used by services and consumers | PSR HTTP construction or response parsing |
| Registry | Name, docs URL, verb, auth permission, cacheability, availability | Request execution |
| `ApiClient` | One REST pipeline and Flickr error mapping | Retry, TLS, redirect policy |
| `OAuth1Signer` | RFC 3986 encoding, sorting, HMAC-SHA1 signature | Sending or token persistence |
| Client v4 adapter | PSR request/response conversion and safe transport wrapping | Flickr semantics |
| Service | Domain method names and small input validation | Parsing/hydration duplicated per method |
| DTO/hydrator | Stable typed public data and mapping | Network calls |
| Cache/token stores | Their narrow persistence contract | Registry or API policy |

### SOLID / DRY / KISS / YAGNI rules

- One class, one concern; inject only real substitution seams: transport, signer, token store, cache, clock/sleeper.
- One raw-call pipeline, normalizer, signature-base builder, registry, error map and generated wrapper source. No copy/paste request branches.
- Prefer explicit final classes and pure helpers. Forbid service locators, magic `__call`, annotations, runtime codegen, generic managers and inheritance trees.
- Allowed patterns only: Facade (`Flickr`), static Factory (`FlickrFactory`), Adapter (`ClientV4Transport`), Registry, Strategy (signer/cache/token store), Builder (REST/multipart), Null Object, Generator (paginator).
- New abstraction/pattern needs a concrete problem, a test and an owner-approved rationale; otherwise it is rejected.

### PHP 8.5 standard

- Every file uses `declare(strict_types=1)`, PSR-4 and one production symbol per file.
- `final` is default; immutable DTO/value objects use promoted `public readonly` properties and DTO v3 where hydration/normalization is useful.
- Explicit parameter/property/return types; public arrays have array-shape/list PHPDoc. `mixed` is allowed only at a validated boundary.
- Use enums for closed Flickr values, `match` for closed branches, `#[\Override]` when overriding. New syntax is used only where it clarifies the contract.
- Forbid dynamic properties, error suppression, loose security comparisons, `eval`, silent catch, globals, static mutable SDK state, `sleep()` in poller tests and untyped public option bags.
- Formatting/static gates: Pint; full PSR-12 PHPCS; PHPStan max + strict rules; PHPMD code-size/design/unused/naming/clean-code; PHP-CS-Fixer PHPDoc rules. No baseline or exclusion without an approved issue.

## 4. Functional rules

### Configuration and REST pipeline

`FlickrConfig` contains only API key/secret, optional callback URL, user agent and public-cache TTL. It does not expose endpoint URLs or response format: v4 fixes JSON REST and package-owned HTTPS endpoints to prevent SSRF and OAuth signature drift. It does not contain retry, circuit-breaker, rate-limit or timeout settings; those belong to client v4.

`FlickrEndpoints` is final and immutable: REST `https://www.flickr.com/services/rest/`; OAuth request/access/authorize under `https://www.flickr.com/services/oauth/`; upload `https://up.flickr.com/services/upload/`; replace `https://up.flickr.com/services/replace/`. Tests use an injected fake `HttpClient`/transport, never a configurable endpoint. The exact endpoint URI used to sign is the URI sent; Flickr passes the portable client-v4 option `allowRedirects: false` for every request, so host/path substitution and redirect credential leakage are not accepted.

`$flickr->api()` returns the public universal gateway. `$flickr->api()->call('flickr.photos.search', [...])` resolves a known registry method; `$flickr->api()->raw('flickr.future.method', HttpMethod::Get, [...], new RawCallOptions(AuthenticationMode::Unauthenticated))` is the explicit future/undocumented fallback. Domain services delegate to the same `Api`; they do not build a second request path.

Known calls use registry auth metadata plus `AuthenticationMode::{Automatic,Authenticated,Unauthenticated}`. `Automatic` signs only when authentication is required; `Authenticated` requires a stored token and bypasses cache; `Unauthenticated` is rejected for required-auth methods. `raw()` accepts only Flickr REST `GET` or `POST`, requires an explicit authenticated/unauthenticated mode (never `Automatic`), and is always non-cacheable. No call option can force-enable cache or override an endpoint, headers, retries or transport settings.

Each REST call must: resolve registry → reject unavailable legacy method → normalize input → add `method`, `api_key`, `format=json`, `nojsoncallback=1` → resolve cache → obtain token only if required → OAuth-sign every authenticated call → build GET query or POST form → send via client v4 → map status/error → parse → cache eligible success.

| Condition | Required result |
| --- | --- |
| Network/client failure | `TransportException`, preserving cause and redacting secret values |
| HTTP 429 | `RateLimitException`, case-insensitive `Retry-After` |
| Empty/malformed/scalar JSON or missing `stat` | `InvalidResponseException` with safe HTTP context |
| `stat=fail`, default policy | `ApiResponseData(ok: false, error: ApiErrorData)` |
| `stat=fail`, `throwOnApiError` | `ApiException` or `AuthorizationException`, retryable according to error map |
| Auth method without a token | `AuthenticationException` before any send |
| Bad file/upload XML | `UploadException` |

### OAuth, cache, pagination and upload

- OAuth flow is a stateless transaction: `begin()` returns immutable `PendingAuthorization` (request token, token secret, permission, issued-at and authorization URL); the consumer stores it server-side and binds it to its own logged-in user/session. `complete(PendingAuthorization, OAuthCallback)` rejects an expired transaction (v4: 10 minutes), `oauth_callback_confirmed != true`, blank verifier, and a callback token not equal to the pending token via `hash_equals`. There is no mutable request-token-secret array in the SDK and no OAuth 1.0a “state” parameter invented by the SDK.
- Nonce and clock are injectable. Signature excludes `oauth_signature`, uses RFC 3986 percent encoding and deterministic repeated-key sort; non-default port is part of the base URI. HMAC-SHA1 is isolated to Flickr OAuth only because the provider mandates it.
- Mandatory access-token stores: in-memory and null. File/encrypted stores ship only after their complete permission/corruption/key/tamper/redaction suite passes; custom stores remain injectable. `PendingAuthorization` persistence is intentionally consumer-owned so the package stays framework-agnostic.
- Cache only successful unauthenticated registry-cacheable GET calls. Never cache authenticated/auth/OAuth/POST/mutation/upload/replace/ticket/failed/disabled calls. Key is deterministic over normalized values and delimiter-safe.
- Paginator is lazy and bounded by `maxPages`, `perPage`, `stopWhenEmpty`; it never retries or sleeps.
- `photos.search` typed input validates `1 <= perPage <= 500` and `page >= 1`; its documentation and paginator surface state Flickr's maximum 4,000 results per query. All Flickr identifiers remain opaque strings, never integer IDs.
- Upload/replace use PSR-7 streams and multipart to `up.flickr.com`, require write token, validate readable regular file and configured size, quote multiword tags, close file handles in `finally`, and parse XML photo/ticket outcomes. Async is an explicit typed boolean. Upload quota/status comes from authenticated `flickr.people.getUploadStatus`, never archive-only `getLimits`; its result is advisory and must not block a valid upload. Upload errors preserve safe Flickr code metadata, including duplicate photo details and `non_pro_desktop_upload_wait_time`, but never auto-retry an upload. Ticket polling has an injected clock/sleeper and terminal completed/failed/invalid/timed-out states; callers must run it outside latency-sensitive web requests.

### Retry, registry and legal compliance

- Flickr sends only GET/POST. The SDK never adds retry middleware or retries an operation itself. Client v4's default retry method allowlist excludes POST, so REST mutations and uploads are not replayed; consumer retry configuration that adds POST is unsupported for Flickr mutations/uploads. A raw POST is likewise never retried by the SDK.
- `resources/method-registry.php` and `resources/api-surface.php` are the single frozen, reviewed sources for method metadata and facade/service mapping. The method registry records official index URL, retrieval date and SHA-256 of the normalized method-name set; the API-surface manifest records the registry-manifest hash it was generated/reviewed against. Generators emit committed wrappers/docs only; runtime never scrapes. A weekly/manual drift job may fetch and compare the public API index, but produces an artifact and fails/reports drift—it never auto-commits or changes a release. A maintainer reviews each difference against method pages before updating the frozen manifest.
- README has a clear compliance section: consumer applications must show the required Flickr disclaimer and required attribution/header when applicable, honour license/owner rules, show no more than 30 Flickr photos per page, provide their own privacy disclosure, and remove requested/private content promptly. Cache adapters store only parsed API metadata (never bytes); consumers remain responsible for invalidation and their product UI/legal duties.

## 5. API coverage baseline — 224 methods

Every row requires registry entry, explicit wrapper, generated docs-index entry and coverage assertion. Typed results are mandatory only for priority workflows; all others remain usable through generic wrapper/raw response.

| Namespace | # | Methods |
| --- | ---: | --- |
| activity | 2 | `userComments`, `userPhotos` |
| auth | 6 | `checkToken`, `getFrob`, `getFullToken`, `getToken`, `oauth.checkToken`, `oauth.getAccessToken` (legacy metadata only) |
| blogs | 3 | `getList`, `getServices`, `postPhoto` |
| cameras | 2 | `getBrandModels`, `getBrands` |
| collections | 2 | `getInfo`, `getTree` |
| commons | 1 | `getInstitutions` |
| contacts | 4 | `getList`, `getListRecentlyUploaded`, `getPublicList`, `getTaggingSuggestions` |
| favorites | 5 | `add`, `getContext`, `getList`, `getPublicList`, `remove` |
| galleries | 10 | `addPhoto`, `create`, `editMeta`, `editPhoto`, `editPhotos`, `getInfo`, `getList`, `getListForPhoto`, `getPhotos`, `removePhoto` |
| groups | 19 | `discuss.replies.{add,delete,edit,getInfo,getList}`, `discuss.topics.{add,getInfo,getList}`, `getInfo`, `join`, `joinRequest`, `leave`, `members.getList`, `pools.{add,getContext,getGroups,getPhotos,remove}`, `search` |
| interestingness | 1 | `getList` |
| machinetags | 5 | `getNamespaces`, `getPairs`, `getPredicates`, `getRecentValues`, `getValues` |
| panda | 2 | `getList`, `getPhotos` (discontinued metadata only) |
| people | 10 | `findByEmail`, `findByUsername`, `getGroups`, `getInfo`, `getLimits`, `getPhotos`, `getPhotosOf`, `getPublicGroups`, `getPublicPhotos`, `getUploadStatus` |
| photos | 60 | `addTags`; `comments.{addComment,deleteComment,editComment,getList,getRecentForContacts}`; `delete`; `geo.{batchCorrectLocation,correctLocation,getLocation,getPerms,photosForLocation,removeLocation,setContext,setLocation,setPerms}`; `getAllContexts`, `getContactsPhotos`, `getContactsPublicPhotos`, `getContext`, `getCounts`, `getExif`, `getFavorites`, `getInfo`, `getNotInSet`, `getPerms`, `getPopular`, `getRecent`, `getSizes`, `getUntagged`, `getWithGeoData`, `getWithoutGeoData`; `licenses.{getAvailable,getInfo,getLicenseHistory,setLicense}`; `notes.{add,delete,edit}`; `people.{add,delete,deleteCoords,editCoords,getList}`; `recentlyUpdated`, `removeTag`, `search`, `setContentType`, `setDates`, `setMeta`, `setPerms`, `setSafetyLevel`, `setTags`; `suggestions.{approveSuggestion,getList,rejectSuggestion,removeSuggestion,suggestLocation}`; `transform.rotate`; `upload.checkTickets` |
| photosets | 18 | `addPhoto`; `comments.{addComment,deleteComment,editComment,getList}`; `create`, `delete`, `editMeta`, `editPhotos`, `getContext`, `getInfo`, `getList`, `getPhotos`, `orderSets`, `removePhoto`, `removePhotos`, `reorderPhotos`, `setPrimaryPhoto` |
| places | 15 | `find`, `findByLatLon`, `getChildrenWithPhotosPublic`, `getInfo`, `getInfoByUrl`, `getPlaceTypes`, `getShapeHistory`, `getTopPlacesList`, `placesForBoundingBox`, `placesForContacts`, `placesForTags`, `placesForUser`, `resolvePlaceId`, `resolvePlaceURL`, `tagsForPlace` |
| prefs | 5 | `getContentType`, `getGeoPerms`, `getHidden`, `getPrivacy`, `getSafetyLevel` |
| profile | 1 | `getProfile` |
| push | 4 | `getSubscriptions`, `getTopics`, `subscribe`, `unsubscribe` |
| reflection | 2 | `getMethodInfo`, `getMethods` |
| stats | 16 | `getCSVFiles`, `getCollectionDomains`, `getCollectionReferrers`, `getCollectionStats`, `getMostPopularPhotoDateRange`, `getPhotoDomains`, `getPhotoReferrers`, `getPhotoStats`, `getPhotosetDomains`, `getPhotosetReferrers`, `getPhotosetStats`, `getPhotostreamDomains`, `getPhotostreamReferrers`, `getPhotostreamStats`, `getPopularPhotos`, `getTotalViews` |
| tags | 9 | `getClusterPhotos`, `getClusters`, `getHotList`, `getListPhoto`, `getListUser`, `getListUserPopular`, `getListUserRaw`, `getMostFrequentlyUsed`, `getRelated` |
| test | 3 | `echo`, `login`, `null` |
| testimonials | 13 | `addTestimonial`, `approveTestimonial`, `deleteTestimonial`, `editTestimonial`, `getAllTestimonialsAbout`, `getAllTestimonialsAboutBy`, `getAllTestimonialsBy`, `getPendingTestimonialsAbout`, `getPendingTestimonialsAboutBy`, `getPendingTestimonialsBy`, `getTestimonialsAbout`, `getTestimonialsAboutBy`, `getTestimonialsBy` |
| urls | 6 | `getGroup`, `getUserPhotos`, `getUserProfile`, `lookupGallery`, `lookupGroup`, `lookupUser` |

Priority typed workflow helpers: `photos.search/getInfo/getSizes/getExif/getRecent`; `people.getInfo/getPhotos`; `favorites.getList`; `galleries.getInfo/getPhotos`; `photosets.getList/getPhotos`; `groups.pools.getPhotos`; `tags.getHotList`; `places.find/getInfo`; upload/replace/checkTickets. Each helper needs sanitized fixture + DTO + hydrator + contract test. No speculative DTO sweep.

## 6. Required test matrix and quality gates

| Suite | Required assertions |
| --- | --- |
| Arch | No framework/Guzzle/old-v2 imports; strict files; final DTOs; no dynamic wrapper; facade-service completeness |
| Registry | Exact 224 fixture names; frozen-manifest provenance/hash; docs/wrapper/facade parity; verb/auth/cache/deprecation metadata; typo suggestion |
| Client adapter | GET query/POST form, portable options, PSR response mapping, network wrapping/redaction, fake teardown |
| OAuth | Fixed nonce/clock vectors, encoding/sorting/repeated values/ports, pending-transaction expiry/callback-confirmation/token-binding/replay failures, zero secret exposure |
| Parser/errors | JSON success/fail/malformed/empty/scalar/missing-stat, XML upload paths, 429/retry-after, auth/error maps |
| DTO/hydrators | Input validation, enums, nested collections, null/missing/extra behaviour and normalization from sanitized fixtures |
| Services/pagination | Required ID/list/tag validation, `photos.search` page/per-page bounds and 4,000-result documentation, canonical override rules, lazy page/max-page/empty-stop behaviour |
| Cache | Hit/miss/stable key and exhaustive bypass for auth/mutation/post/upload/ticket/failures |
| Upload/tickets | File safety, multipart bytes/headers/tag quoting, async outcomes, `getUploadStatus` advisory quota, error-code metadata/duplicate/wait-time, resource closure and all poller terminal states |
| Consumer smoke | Clean Composer install with client-v4 fake; typed/raw explicit-auth/OAuth pending-transaction/upload examples without network |
| Real opt-in | Public search and authenticated `test.login`/upload-status only with all `FLICKR_REAL_TESTS` credentials; destructive tests require explicit disposable-account approval |

PHPUnit uses strict Arch/Unit/Integration suites and fails on risky, warning, notice, deprecation, PHPUnit deprecation, output and empty suite. Unit coverage and combined release Clover coverage must each be **>=85.00% statements**, with no source exclusion. `composer ci` must run validate, all lint, audit, registry/index checks, coverage enforcement and consumer smoke.

## 7. GitHub Actions, branch and release contract

Adopt current client/DTO topology, using the organization-supported self-hosted Linux/X64 Docker PHP 8.5 runner. Local Docker green is not proof that Actions will work.

| Workflow | Trigger | Gate |
| --- | --- | --- |
| CI | PR to master/develop | validate; Pint/PHPCS/PHPStan/PHPMD/CS-Fixer matrix; Composer audit/OSV/dependency-review/Gitleaks/Semgrep matrix; Arch/Unit/Integration coverage matrix; 85% gate; Codecov/Sonar |
| Post-merge CI | master/develop push | validate, test/coverage matrix and uploads |
| CodeQL | PR/push/weekly | Actions analysis + SARIF |
| Workflow audit | workflow changes/weekly | Actionlint + Zizmor + SARIF |
| Scorecard | push/weekly | OpenSSF Scorecard + SARIF |
| Commitlint / semantic PR / labeler / stale | PR or scheduled | conventional quality and least privilege |
| Link check / registry drift | weekly/manual | docs URLs and registry fixture/index drift |
| Release | immutable `v*.*.*` tag | tag must be ancestor of origin/master; rebuild, full validate/lint/test/coverage, Trivy fs+image, SBOM, GitHub Release and Packagist verification |

All actions/containers are immutable SHA/digest-pinned with reviewed version comments; minimal permissions; `persist-credentials: false`; vendor cache keyed to lock; concurrency cancels ordinary CI but never release. Workflow upgrades must pass Actionlint and Zizmor.

Flow: feature from `develop` → green PR to `develop` → `release/4.0.0` metadata-only PR to `master` → green master → annotated tag → green release workflow → verify GitHub Release, SBOM and Packagist → master-to-develop merge-back PR. Branch protection requires the PR checks and blocks direct/force pushes.

## 8. Release definition of done

1. Public Packagist-compatible install resolves client ^4 and DTO ^3; no local/path dependency.
2. 224 registry + wrappers + API-index entries verify exactly; unavailable methods are explicit.
3. Every test, lint, audit, scan, smoke and required remote check is green: no skipped required job, baseline or waiver.
4. Unit and release combined coverage are each >=85.00%.
5. Credentials-free tests fully cover protocol behaviour; opt-in real tests never substitute for fakes and fixtures; no destructive live test without approval.
6. Docs cover client-v4 setup and safe retry boundary, public search limits, raw explicit-auth API, OAuth pending-transaction/token-store boundary, errors, cache, upload, fake, security, Flickr API compliance and no-BC v4 policy.
7. No secret, temporary artifact, debug output or generated junk is in the release tree.
8. Remote branch protection, tag ancestry, GitHub Release, SBOM and Packagist publication are verified before declaring release complete.
