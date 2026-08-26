# Flickr SDK v4.0.0 — Implementation Specification

> This is the execution specification for v4.0.0. The archived Flickr SDK is **reference material only**. A component may be adopted only after it passes the v4 architecture, `client:^4`, `dto:^3`, security and test requirements below. It is never copied by default.

## 1. Decision protocol: learn, then choose

Every archived component must receive one of these decisions before implementation:

| Decision | When allowed | Examples |
| --- | --- | --- |
| **Adopt behaviour** | Domain rule is correct, complete and fixture-testable | Flickr error mapping, 224 registry data, OAuth endpoint flow, pagination semantics, photo URL rules |
| **Redesign implementation** | Behaviour is useful but old dependency/API/security model is wrong | client v2 transport → client v4 adapter; DTO v1 → DTO v3; Guzzle multipart → PSR-7 multipart; old fake → client v4 fake adapter |
| **Reject** | Legacy, unsafe, duplicate or against YAGNI | client v2 contracts, Guzzle option bags, legacy `api_sig` auth, Panda live support, `__call`, general XML REST, duplicate retry/rate-limit |

No archive file is ported until its public behaviour, dependencies, failure modes, fixtures and tests are understood. A green old test does not prove its contract is right for v4.

## 2. Business requirements

### 2.1 Consumer outcomes

1. A PHP 8.5 consumer can configure an API key/secret and call public Flickr REST methods without a framework.
2. The consumer can inject a client v4 `HttpClient`; its TLS, proxy, timeouts, retries, redirects, logging and resilience settings remain authoritative.
3. All 224 archived official REST methods are discoverable in the registry, documented and callable through an explicit service wrapper. An unknown future method is callable through `raw()`.
4. The SDK supports OAuth 1.0a request-token, authorization URL and access-token exchange, then signs authenticated calls with Flickr-required HMAC-SHA1.
5. Users can upload, replace and poll async tickets with write permission, without loading a file unnecessarily or leaking credentials.
6. Priority workflows return typed DTO v3 objects; all other methods return a stable generic API response instead of an incomplete guessed DTO.
7. Consumers can test their own code with `Testing\FlickrFake`, which is built on client v4 fakes and never makes an accidental network request.
8. A public PSR-16 cache can improve eligible public GET calls but can never expose/private-cache authenticated data.
9. The package documents and supports compliant integration, but does not pretend to enforce consumer UI/legal obligations: no photo-binary fetch/cache feature, clear Flickr terms/attribution guidance, and no abstraction that hides those duties.

### 2.2 Product rules

- Version is `4.0.0`; there is no v2 API compatibility, deprecated alias, migration helper or Guzzle option support.
- General REST is JSON only: `format=json` and `nojsoncallback=1`. XML is accepted only for upload/replace responses.
- Flickr API docs are the authority for field semantics. Archive metadata is an initial frozen baseline, not automatic proof that a live endpoint still behaves the same.
- No automatic retry, rate limit, circuit breaker, timeout, logging, queue, framework integration or concurrency implementation belongs in Flickr. Configure such policy in client v4.
- Flickr REST supports GET and POST only. Flickr never configures retry middleware; POST mutations/uploads are never replayed by this package. A consumer client that opts into POST retries is unsupported for Flickr mutations/uploads.
- Legacy pre-OAuth `flickr.auth.*` and discontinued Panda entries remain documented as unavailable; they must fail before a network request.

## 3. Technical requirements

### 3.1 Runtime and public construction

```json
{
  "require": {
    "php": ">=8.5",
    "jooservices/client": "^4.0",
    "jooservices/dto": "^3.0",
    "nyholm/psr7": "^1.8",
    "psr/http-factory": "^1.1",
    "psr/simple-cache": "^3.0"
  }
}
```

`nyholm/psr7` and `psr/http-factory` are direct dependencies because Flickr must create PSR-7 requests/streams, especially multipart streams. Do not depend accidentally on client v4's transitive package. `ext-curl` remains a transitive client-v4 requirement for its default transport; consumers may inject a supported client v4 transport.

```php
$http = ClientBuilder::create()
    ->withTimeout(30)
    ->build();

$flickr = FlickrFactory::make(
    config: new FlickrConfig(apiKey: $key, apiSecret: $secret),
    http: $http,
    cache: new Psr16Cache($cache),
    tokens: new InMemoryTokenStore(),
);
```

`FlickrFactory` must require/inject `HttpClient`; it may offer an explicit convenience `makeDefault()` only when its default client-v4 configuration is fully documented. It must never read environment variables or choose a concrete non-client transport silently. `FlickrConfig` contains no endpoint override: `FlickrEndpoints` owns the exact final HTTPS URIs for REST, OAuth request/access/authorize, upload and replace. Tests inject a fake client/transport instead of changing endpoint host/path.

### 3.2 Cross-cutting code rules

- Every file has `declare(strict_types=1)`, a PSR-4 namespace, one production symbol, explicit visibility and a typed public signature.
- Production classes are `final`; DTO/value objects use `readonly` promoted properties. Services are composed, not inherited, except one small `AbstractApiService` with no state beyond its `Api` caller.
- Use enum values and `match` for closed domains. All collection types are documented with `list<T>` or `array<string,T>` shapes.
- Public input is validated at the boundary. Internal functions do not revalidate the same value without a security reason.
- No global mutable Flickr state. Client v4's static fake state is isolated behind explicit `FlickrFake::close()`/test teardown.
- Error messages use a single `SensitiveDataRedactor`; API secret, token secret, OAuth token, verifier, signature and Authorization-like values are always masked.

## 4. Required code structure and skeleton

```text
src/
├── Flickr.php                         # final facade, explicit service accessors only
├── FlickrFactory.php                  # composition root; injected client v4 + PSR-17 bundle
├── Config/{FlickrConfig,FlickrEndpoints}.php # endpoints are final package constants
├── Contracts/
│   ├── Auth/{Signer,TokenStore}.php
│   ├── Cache/Cache.php
│   └── Internal/{Transport,Clock,Sleeper}.php
├── Auth/{OAuth1Signer,OAuth1Authenticator,PendingAuthorization,OAuthCallback,
│         InMemoryTokenStore,NullTokenStore}.php
├── Client/
│   ├── ClientV4Transport.php          # sole client-v4 adapter
│   ├── Psr17Factories.php              # request/stream/URI factories owned by Flickr
│   ├── FlickrRequestBuilder.php       # GET query / POST form; no Guzzle options
│   ├── MultipartRequestBuilder.php    # PSR-7 stream body, boundary and closure owner
│   ├── ApiClient.php                  # single REST orchestration pipeline
│   └── FlickrResponseParser.php        # JSON REST + upload XML only
├── Api/
│   ├── Api.php                         # public universal gateway
│   ├── ApiCallOptions.php               # known-method policy; never client options
│   ├── RawCallOptions.php               # required explicit raw auth; never cacheable
│   └── InternalApi.php                  # narrow service-facing contract, if needed
├── Cache/{NullCache,Psr16Cache,CacheKeyResolver}.php
├── Metadata/{FlickrMethodDefinition,FlickrMethodRegistry}.php
├── Services/{AbstractApiService,...43 explicit services}.php
├── DTO/{Auth,Common,Photos,People,...}.php
├── Hydrators/{Photo,People,Photoset,Favorite,Group,Tag,Place,UploadTicket}.php
├── Enums/{AuthPermission,HttpMethod,CachePolicy,...}.php
├── Exceptions/{FlickrException,TransportException,...}.php
├── Pagination/{Paginator,PaginationOptions}.php
├── Upload/{UploadService,TicketPoller,TicketStatus,UploadOptions,UploadResultData}.php
│           (advisory upload-status lookup lives on UploadService::uploadStatus(), not a separate resolver class)
├── Support/{ParameterNormalizer,QueryString,SignatureBaseStringBuilder,
│           SensitiveDataRedactor,FileValidator,PhotoUrlBuilder}.php
└── Testing/FlickrFake.php
resources/{method-registry.php,api-surface.php} # reviewed frozen manifests + provenance
tests/{Arch,Unit,Integration,Fixtures}/
tools/{verify-method-registry,generate-api-index,verify-api-index,coverage-enforce,
       coverage-merge,check-registry-drift,consumer-smoke}.php
```

Core contracts must remain narrow:

```php
interface FlickrTransport
{
    public function send(RequestInterface $request, RequestOptions $options): RawResponseData;
}

interface TokenStore
{
    public function get(): ?AccessTokenData;
    public function put(AccessTokenData $token): void;
    public function forget(): void;
}

interface FlickrCache
{
    public function get(string $key): ?ApiResponseData;
    public function put(string $key, ApiResponseData $value, int $ttl): void;
}
```

`ApiClient` is the only REST pipeline. `Api` is the public gateway over it. Services may not call the transport, construct OAuth parameters, parse response bodies or compute cache keys themselves.

### 4.1 Public API surface and consumer usage

`Flickr` is the small facade. `Api` is the universal/public gateway; `ApiClient` is intentionally internal and is never returned to consumers.

```php
final class Flickr
{
    public function api(): Api;
    public function oauth(): OAuth1Authenticator;
    public function photos(): PhotosApi;
    public function people(): PeopleApi;
    public function photosets(): PhotosetsApi;
    public function uploads(): UploadService;
    // ... all accessors below are generated from resources/api-surface.php
}

final class Api
{
    /** Calls a known, available registry method. */
    public function call(
        string $method,
        array $parameters = [],
        ?ApiCallOptions $options = null,
    ): ApiResponseData;

    /** Future/undocumented method: the caller states the HTTP verb explicitly. */
    public function raw(
        string $method,
        HttpMethod $httpMethod,
        RawCallOptions $options,
        array $parameters = [],
    ): ApiResponseData;

    public function describe(string $method): ?MethodInfo;
}
```

```php
final class FlickrFactory
{
    public static function make(
        FlickrConfig $config,
        HttpClient $http,
        ?TokenStore $tokens = null,
        ?FlickrCache $cache = null,
        ?Psr17Factories $psr17 = null,
    ): Flickr;
}

abstract class AbstractApiService
{
    public function __construct(protected readonly Api $api) {}

    /** @param array<string, mixed> $parameters */
    final protected function call(string $method, array $parameters = []): ApiResponseData;
}

final class PhotosApi extends AbstractApiService
{
    public function search(SearchPhotosData $query): SearchResultData;
    public function getInfo(string $photoId): PhotoInfoData;
    public function getSizes(string $photoId): list<PhotoSizeData>;
    public function delete(string $photoId): ApiResponseData;
}

final class ApiClient
{
    /** Internal only; all public calls enter through Api. */
    public function call(FlickrMethodDefinition $method, array $parameters, ApiCallOptions $options): ApiResponseData;
}
```

`Psr17Factories` is a small Flickr-owned readonly value object around `RequestFactoryInterface`, `StreamFactoryInterface` and `UriFactoryInterface`. Its explicit direct dependency prevents Flickr multipart/request construction from relying on an undocumented client-v4 internal factory. The default uses the directly required Nyholm factory; tests inject deterministic factories.

The frozen API-surface manifest declares exactly these final facade names: `activity`, `legacyAuth`, `blogs`, `cameras`, `collections`, `commons`, `contacts`, `favorites`, `galleries`, `groups`, `groupsDiscussReplies`, `groupsDiscussTopics`, `groupsMembers`, `groupsPools`, `interestingness`, `machinetags`, `panda`, `people`, `photos`, `photosComments`, `photosGeo`, `photosLicenses`, `photosNotes`, `photosPeople`, `photosSuggestions`, `photosTransform`, `photosets`, `photosetsComments`, `places`, `prefs`, `profile`, `push`, `reflection`, `stats`, `tags`, `test`, `testimonials`, `urls`, plus the non-registry components `api`, `oauth` and `uploads`. `legacyAuth()` and `panda()` are explicit metadata-only services whose methods fail before network; `uploads()` owns upload, replace and ticket checks. No old aliases such as `authApi()`, `authOauthApi()` or `photosUpload()` exist, and the archived `tokens()` accessor is intentionally dropped: the token store is injected once at `FlickrFactory` and consumers hold their own store instance.

Use a typed service where one exists; use `api()` for generic known calls, method discovery and the explicit future-method fallback:

```php
// Preferred typed workflow.
$page = $flickr->photos()->search(new SearchPhotosData(text: 'sunset', perPage: 20));
$photo = $flickr->photos()->getInfo('123');

// Complete known surface without a dedicated typed helper.
$response = $flickr->api()->call('flickr.tags.getHotList', ['count' => 20]);

// Explicit raw fallback: no guessed verb or hidden transport policy.
$response = $flickr->api()->raw(
    method: 'flickr.future.method',
    httpMethod: HttpMethod::Get,
    parameters: ['query' => 'value'],
    options: new RawCallOptions(AuthenticationMode::Unauthenticated),
);

// Metadata access is local only.
$definition = $flickr->api()->describe('flickr.photos.search');
```

`ApiCallOptions` contains `AuthenticationMode` (`Automatic`, `Authenticated`, `Unauthenticated`), cache bypass and `throwOnApiError`. It cannot force-enable cache or set a per-call TTL. `Automatic` is available only for known methods and signs only when registry metadata requires it; `Authenticated` demands a stored token and bypasses cache; `Unauthenticated` rejects a required-auth method before send.

`RawCallOptions` contains only an explicit `Authenticated` or `Unauthenticated` mode and `throwOnApiError`; `Automatic` is rejected. `raw()` accepts only Flickr REST GET/POST and is unconditionally non-cacheable. Neither option object exposes timeout, proxy, TLS, headers, retries or arbitrary client options; those remain client-v4 configuration.

## 5. Detailed implementation requirements

### 5.1 Client v4 adapter and response body handling

1. `ClientV4Transport` accepts `HttpClient` and PSR-17 factories/bundle supplied by factory.
2. `FlickrRequestBuilder` creates immutable PSR-7 requests. GET parameters are RFC-3986 query values; POST REST parameters are `application/x-www-form-urlencoded` bodies, not old `form_params` option arrays.
3. Client request options are only portable `RequestOptions`. `ClientV4Transport` supplies `allowRedirects: false` for every Flickr request; this fixed credential/signature security delta cannot be overridden. Flickr's per-call options can select only their defined auth/cache/error policy and cannot smuggle old client/Guzzle options.
4. `HttpClient::send()` returns HTTP status responses normally. The adapter converts headers/body/status to `RawResponseData`; it does not throw merely for 4xx/5xx.
5. Read response bodies using client v4 `Response::from($response)` body guard, then map size/stream errors to `TransportException` or `InvalidResponseException`. Never cast an unbounded stream to string directly.
6. Preserve repeated query values and keys with dots. Never use PHP `parse_str()` for Flickr protocol/query parsing because it rewrites dotted/spaced keys and collapses semantics.
7. `FlickrEndpoints` supplies the exact immutable request URI. `FlickrRequestBuilder` may not accept an absolute caller URI or replace host/path; `ClientV4Transport` passes `allowRedirects: false`. OAuth is signed against this exact URI and the same URI is sent.

### 5.2 Registry and service generation

- `resources/method-registry.php` is reviewed data only. It contains the 224 method records plus official index URL, retrieval date and SHA-256 of the normalized method-name set. Each record contains name, documented URL, HTTP method, auth requirement/permission, default cacheability, availability and deprecation reason.
- `resources/api-surface.php` is the separate reviewed mapping from service class/accessor to registry method names and records the reviewed registry-manifest SHA-256. It is the authority for every facade accessor and wrapper; no archive names such as `authApi()` or `photosUpload()` are inherited accidentally.
- Registry loads once without reflection. `find()` returns known definitions only. `raw()` does not invent a registry definition: its caller supplies GET/POST and explicit auth mode, and it has no cache policy.
- Generators consume only the two frozen manifests and emit committed PHP wrappers/facade/docs index. They are deterministic, their outputs are verified in CI and never run at runtime.
- `check-registry-drift` is a scheduled/manual, read-only comparison against Flickr's public API index. It uploads a diff artifact and reports/fails drift; it never writes a manifest, opens a release or accepts a change. A maintainer must inspect changed method pages and update manifest, provenance and fixtures in a normal PR.
- Wrapper tests reflect every public service method/accessor and assert exact method string, verb, authentication policy and unavailable-before-network behaviour. A generated docs index is verified against the same manifests.

### 5.3 OAuth implementation

1. `OAuth1Authenticator::begin(AuthPermission)` requests the token and returns immutable `PendingAuthorization`: request token, request-token secret, permission, issued-at and authorization URL. It verifies `oauth_callback_confirmed === 'true'`; any other value fails closed.
2. The caller persists `PendingAuthorization` only server-side (for example, a session bound to its authenticated user); the SDK provides no global or mutable request-token-secret store. `PendingAuthorization` is valid for a v4-defined maximum of 10 minutes.
3. `complete(PendingAuthorization $pending, OAuthCallback $callback)` rejects expiration, blank verifier, malformed callback and a callback token that does not equal the pending token using `hash_equals`; only then does it sign the access-token request with the pending secret. A second completion of the same pending transaction is consumer-store responsibility and must be rejected by the consumer binding/store.
4. Build OAuth parameters with injectable `Clock` and `NonceGenerator` in tests; production uses `time()` and cryptographically secure `random_bytes`. Normalize all request and OAuth parameters; exclude only `oauth_signature`; sort percent-encoded key/value pairs by byte order; normalize base URL (lowercase scheme/host, omit default port, retain non-default port, omit query/fragment).
5. Sign with `base64_encode(hash_hmac('sha1', baseString, encodedConsumerSecret.'&'.encodedTokenSecret, true))`. HMAC-SHA1 is quarantined to this Flickr-required signer and is not a reusable application crypto facility.
6. OAuth request-token/access-token endpoints use the documented query/form contract exactly. Callback defaults to `oob` only when no callback URL is explicitly configured. Never put OAuth data in cache, tracing, logs, debug output, API docs, fake errors or exception messages.

### 5.4 Multipart upload and XML

- Do not port archive Guzzle multipart arrays. `MultipartRequestBuilder` creates the boundary, per-part headers and a seekable PSR-7 stream/resource body. It owns closure through `try/finally`.
- Validate `realpath`, readable regular file, no directory/symlink escape policy as agreed, configured maximum bytes and a non-empty basename. File type remains Flickr's responsibility unless a documented allowlist is later required.
- Sign all non-file upload fields; do not include binary file contents in OAuth signature. Send `Content-Type: multipart/form-data; boundary=…` and never overwrite a caller-supplied boundary accidentally.
- Upload/replace DTOs expose `async` explicitly. XML parsing returns either a validated photo ID or ticket IDs. A failed upload maps safe Flickr error-code metadata, including duplicate-photo fields and `non_pro_desktop_upload_wait_time`; it never causes an automatic upload retry.
- `UploadService::uploadStatus()` calls authenticated `flickr.people.getUploadStatus` only on explicit consumer request, never caches its response, and never rejects an upload solely from its advisory values. Archive `CachedUploadLimitResolver` and `people.getLimits` are not used for upload enforcement.
- XML parser uses `LIBXML_NONET`, scoped libxml error handling and rejects DTD/entity declarations, missing `rsp stat`, `stat=fail`, missing/empty photo/ticket IDs and excessive/invalid payloads.

### 5.5 DTO/hydrator policy

- DTOs exist only for common envelope/error/pagination, auth tokens, priority photo/person/favorite/gallery/photoset/group/tag/place and upload/ticket workflows.
- Hydrators are small deterministic mappers from a known JSON shape. They must not silently coerce malformed arrays to valid-looking DTOs.
- Every DTO class has an intentional unknown-key/null policy through DTO v3 context and a fixture test. Keep raw original data in `ApiResponseData` for fields not yet modelled.
- `PhotoUrlBuilder` returns a URL only with complete validated metadata; otherwise it returns `null`, never a malformed URL string.

### 5.6 Cache and polling corrections from archive behaviour

- Cache key uses a versioned prefix plus SHA-256 over recursively normalized typed values. Associative keys sort; list ordering is preserved.
- Cache bypass is absolute for authenticated calls, registry auth methods, raw calls, POST, mutation, upload/replace, OAuth and **ticket polling**. No call option can force-enable it. This intentionally corrects the archive's ability to force-cache `photos.upload.checkTickets`.
- Cache only a successful `stat=ok` public GET after parsing. It stores `ApiResponseData` metadata only—never image bytes, credentials or tokens. Failed, malformed, rate-limited and authorization responses never populate cache.
- `TicketPoller` uses a monotonic/injected clock and injected sleeper; reject non-positive wait/interval, do not busy-loop, and return explicit terminal state rather than swallowing unknown status. Documentation marks it unsuitable for a latency-sensitive web request.

### 5.7 Testing fake built on client v4

`FlickrFake` is a semantic convenience layer, not a second HTTP fake engine.

1. It creates/injects an `HttpFakeRegistry` through `ClientBuilder::fake($registry)` and lets client v4 block every stray request by default.
2. It queues `TestResponseSequence` responses in expected request order and uses `ClientBuilder::recorded()` plus a Flickr-safe query/form decoder to assert method/parameters after sending.
3. Client v4 fake matching can match URI/query but not POST form body. Therefore `FlickrFake` must not claim it dynamically routes POST responses by `flickr method`; it verifies ordered semantic calls and reports mismatch with the recorded request.
4. It provides `close()` and PHPUnit teardown helper that call `ClientBuilder::clearFake()` even after a test fails. Parallel test processes must own separate registries.
5. It decodes both GET query and POST form without `parse_str()`; binary/multipart assertions inspect headers/body boundaries and recorded request metadata, not raw secrets.

### 5.8 Flickr API terms and attribution boundary

The SDK is not a web application and cannot automatically inject a page header, limit a consumer's rendered gallery or delete a consumer's cache. Therefore v4 deliberately does not add a fake “compliance manager”. Instead, the README and consumer smoke fixture must state these non-negotiable integration duties:

1. Respect photo-owner license/restrictions; the API response and a photo URL do not grant reuse rights.
2. Render no more than 30 Flickr user photos per consumer page; do not use Flickr as generic image hosting.
3. Prominently display Flickr's required non-endorsement disclaimer and use the required attribution/header where the API terms require it; do not imply endorsement or use Flickr branding without permission.
4. Publish the consumer's privacy disclosure, reflect a photo becoming private as soon as reasonably possible, and remove owner-requested content/data within the terms' stated window.
5. Cache adapters only receive parsed API metadata, never binary photos. Cache invalidation and UI/legal compliance remain the consumer's responsibility.

No runtime test claims legal compliance. Documentation tests assert that these obligations and the no-binary-cache guarantee remain present in the published README.

## 6. Test cases

### 6.1 Happy-path cases

| Area | Case | Required assertion |
| --- | --- | --- |
| Factory | Injected fake client and null cache | Public facade/service is usable without network; only package-owned endpoint URIs are used |
| Public REST | `photos.search` GET | Correct endpoint/query/json flags; `stat=ok`, page/per-page bounds and pagination map |
| Auth REST | `photos.delete` POST | Form body, OAuth token/signature, write/delete permission and success map |
| OAuth | Deterministic begin/complete transaction | Exact base string/signature, callback confirmation/token binding and authorization URL |
| Registry | Every 224 entry | Manifest provenance/hash, facade/wrapper/docs/fixture parity and exact dispatch |
| DTO | Search/info/sizes/exif/person/etc. fixtures | Correct nested DTOs/enums/normalization |
| Cache | Same normalized public request twice | One HTTP call, one successful cache write, same response semantics |
| Pagination | Three pages | Lazy order and stop at declared page count |
| Upload | Readable small file, write token, valid XML photo/ticket | Correct multipart, typed async result, `getUploadStatus` advisory path and closed stream |
| Tickets | Pending then complete | Expected sleeping intervals and completed result |
| Fake | Queued public GET and POST response | Client v4 records exact Flickr method and selected parameters |

### 6.2 Unhappy-path cases

| Area | Case | Required result |
| --- | --- | --- |
| Config | Blank key/secret, CR/LF user agent, negative TTL | `ConfigurationException` before send; endpoint host/path cannot be configured |
| Auth | No token, token missing secret, insufficient registry permission or raw `Automatic` | `AuthenticationException`/validation error before send |
| OAuth | Callback not confirmed, expired pending auth, blank verifier, callback token mismatch | Authentication error before access-token send |
| Registry | Unknown normal service method / unavailable legacy/Panda | Clear unsupported error, no network |
| Transport | Fake network exception, unreadable/oversized body | Wrapped redacted `TransportException` |
| Parser | Empty, malformed JSON, scalar JSON, missing/invalid `stat` | `InvalidResponseException` |
| API failure | `stat=fail`, throw disabled/enabled | Envelope then proper Flickr exception/error map |
| Rate limit | 429, mixed-case Retry-After, absent/invalid header | `RateLimitException` with correct nullable delay |
| Cache | Cache backend get/set throws | Safe cache exception policy; never return fabricated response |
| Upload | Missing path, directory, unreadable/oversize file, no token | `UploadException`/auth error; no upload request |
| Upload XML | `stat=fail`, malformed XML, missing id, duplicate/wait-time error metadata | `UploadException`/invalid response; stream closed; no automatic retry |
| Poller | Empty tickets, negative timeout, unknown status, deadline exceeded | Validation error or explicit invalid/timed-out result |
| Fake | No matching/empty fake sequence | Client v4 readable stray/empty-sequence failure |

### 6.3 Weird and strange cases

| Area | Case | Required assertion |
| --- | --- | --- |
| Query | Dotted keys, `%2B` vs `+`, spaces, UTF-8, repeated values, array list order | No key rewriting/loss; RFC-3986 deterministic signing |
| OAuth | Default vs non-default port, uppercase method, duplicate encoded keys, empty token secret | Exact canonical base string/signature |
| Endpoint | REST/OAuth/upload URI has a different host/path or redirect response | SDK builds/signs only trusted URI; no caller endpoint override or application redirect follow |
| Raw | GET/POST with explicit auth mode; PUT/DELETE or omitted/automatic mode | Valid dispatch with no cache; validation error before send for disallowed form |
| REST | Caller supplies `method`, `api_key`, `format`, `nojsoncallback` | SDK canonical values win; cannot redirect request semantics |
| DTO | `total` numeric string, null optional fields, unknown fields, empty nested array | Documented mapping without false defaults |
| Cache | Same associative params different order; delimiter-injected values; different list order | Same first key, collision-resistant key, distinct list key |
| Response | BOM JSON, 204/no body, huge response, mismatched content type, `stat=ok` with unusual shape | Explicit supported outcome, never silent corruption |
| Upload | Filename with spaces/quotes/unicode, tags with spaces/quotes, transport throws mid-send | Safe disposition, correctly escaped headers/tags, handle closure |
| Fake | Out-of-order queued responses, POST body has Flickr method, global fake after failing test | Semantic assertion names mismatch; no fake leakage |
| Paginator | `pages=0`, current page > pages, empty page before last page, `maxPages=1` | Bounded, documented stop behaviour with no infinite loop |

### 6.4 Security cases

| Threat | Test/control |
| --- | --- |
| Secret disclosure | Assert key/secret/token/verifier/signature absent from every exception, fake failure, fixture, debug output and loggable string |
| Header injection | API/user-agent/custom headers containing CR/LF rejected by client v4 boundary |
| SSRF/endpoint tampering | Endpoints are final package constants; no config/call override; exact signed URI is sent |
| OAuth replay/predictability | Production nonce comes from `random_bytes`; tests inject deterministic nonce only |
| OAuth callback substitution | `hash_equals` token binding, confirmation and expiry reject a foreign/replayed callback before exchange |
| Signature confusion | Reject unsorted/ambiguous normalizer inputs; test encoded-key/value ordering and key separation |
| Cache privacy | Auth/token/header/mutation/ticket responses never read/write cache; cache keys contain no plaintext secret |
| XML entity attack | Reject DOCTYPE/external entity/network entity and malformed entity expansion input under `LIBXML_NONET` |
| Resource exhaustion | Enforce client response-body guard, bounded parser depth/size policy, max upload size, bounded poller |
| File access | Reject unreadable/non-regular/outside-policy upload targets; close handle on all paths |
| Supply chain | Composer audit + OSV + dependency review; SHA/digest-pin actions/images; SBOM on tag |
| Workflow abuse | Actionlint/Zizmor/CodeQL; minimal permissions; no `pull_request_target` checkout of untrusted code |
| Mutation replay | SDK adds no retry middleware; POST REST/upload remain unreplayed with client v4's default GET/HEAD/PUT/DELETE/OPTIONS retry allowlist |

## 7. Acceptance gates by implementation increment

1. **Core increment:** P0/P1 quality tooling plus client-v4 adapter unit and architecture tests green.
2. **Protocol increment:** Registry, OAuth vectors, raw pipeline, parser and cache tests green; 224 parity automated.
3. **Domain increment:** All generated wrappers; priority DTO/hydrator fixtures; fake consumer flow green.
4. **Upload increment:** Multipart, XML, ticket polling, security/resource tests green.
5. **Release increment:** `composer ci`, 85% Unit + combined release coverage, consumer smoke, manifest provenance/drift checks, compliance-documentation assertion, Composer audit/OSV/Gitleaks/Semgrep, Actionlint/Zizmor/CodeQL, Trivy, SBOM and remote required checks all green.

No increment is “done” because code compiles, an old test passes, or a local happy path works. It is done only when the stated tests and quality gates pass without an exclusion, warning waiver or manual patch to generated output.
