# v2.0.0 Roadmap

This document defines the plan for `jooservices/flickr` v2.0.0.

Scope status:

- Implementation in progress (see Delivery Phases).
- Written so a developer new to this codebase can read a section and know what to build, why it exists, and roughly how to start.
- Where something could not be verified, it is marked **unverified** instead of being written as fact.
- Earlier planning drafts proposed features that turned out unnecessary or unsafe. Those are recorded in "Superseded Ideas And Why" so they are not silently re-proposed.

## Goals

- Align with `jooservices/client` 2.x and `jooservices/dto` 1.3.x.
- Adopt `jooservices/exceptions` `^0.5` as the shared exception foundation.
- Deliver major, user-visible improvements in reliability, security, and developer experience.
- Keep architecture aligned with SOLID, DRY, KISS, and YAGNI.
- Stay framework-agnostic: no Laravel/Symfony coupling, no service providers.
- **Prefer configuring an existing `jooservices/client` capability over building a new one in this SDK.**

## Principles And Design Rules

- SOLID: keep services focused and contract driven.
- DRY: extract shared validators and normalizers where duplication exists.
- KISS: prefer explicit code over magical abstractions.
- YAGNI: only add abstractions when an active pain point justifies them.
- Patterns to favor when useful: Adapter (transport), Strategy (retry/cache), Factory (wiring), Registry (method metadata).

---

## Dependency Baseline

Re-checked against Packagist and local sibling repos on 2026-07-21:

| Package | Target constraint | Locked before v2 work | Latest available | Action |
|---|---|---|---|---|
| `jooservices/client` | `^2.0` | v1.4.0 (HEAD still declared `^1.4`; working tree had unfinished `^2.0` bump) | **v2.0.0 published** | Finish the upgrade: lock to v2.0.0 and fix any transport breakages. |
| `jooservices/dto` | `^1.3` | v1.2.0 | **v1.3.0 published** | Safe minor bump. |
| `jooservices/exceptions` | `^0.5` | not required | **v0.5.0 on Packagist** (package CHANGELOG mentions 1.0.0; published tag is `v0.5.0`) | Require and adopt bases now — Feature 1 is unblocked. |
| `jooservices/state-machine` | not required | — | On Packagist as **dev-master / dev-develop only** (no stable tag yet) | **Do not depend.** Ticket poller uses a plain enum (YAGNI). Tag a stable release on that repo separately if consumers need `^1.0`. |

**Phase 0 first task:** lock client/dto, require exceptions, re-confirm client 2.0 middleware APIs against the installed package (docs can drift).

Confirmed from `jooservices/client` v2.0.0 source:

- `RetryConfig` default `retryableExceptions` is **only** `NetworkConnectionException` (not `TimeoutException`).
- `withRetry`, `withCircuitBreaker`, `withRateLimit`, `ClientBuilder::fake()`, `TestResponse`, and `NullSleeper` exist.
- `Psr16RateLimitStore` uses PSR-16 get/set (best-effort across workers, not atomic). Default in-memory store is per-process only.
- This SDK already wires `withRetry` when `FlickrConfig::$retryTimes > 0` in `JooClientTransport`. Circuit breaker and rate limit are not wired yet.

---

## Proposed Major Features

### 1. Exception Model Unification

**What:** Move the SDK exception hierarchy onto `jooservices/exceptions`, and add structured fields (`httpStatus`, `retryable`) while keeping `apiCode()` for BC.

**Why:** `FlickrException` → `ApiException` already exists with `apiCode()`. Missing pieces are shared ecosystem base, HTTP status, and retryable hint. Callers today can branch on class; structured fields make retry/HTTP handling machine-readable.

**How:**

1. `FlickrException` extends `JOOservices\Exceptions\Base\AbstractJOORuntimeException` (shared base forbids HTTP semantics — keep HTTP fields on Flickr subclasses only).
2. Extend `ApiException` with `?int $httpStatus` and `bool $retryable`; keep `apiCode()`.
3. Add `FlickrErrorCodeMap` (auth `96–99`, retryable `105` only unless method docs prove more).
4. Wire the map in `FlickrClient` when throwing.

Do **not** make `ApiException extend \RuntimeException` directly — that would break `catch (FlickrException)`.

---

### 2. Resilience: Retry + Circuit Breaker — via `jooservices/client`

**What:** Configure client retry and circuit-breaker middleware. Do not reimplement retry in this SDK.

**Facts:**

- Client default `retryableMethods` excludes `POST` — safe for Flickr mutations as long as registry `HttpMethod` is accurate.
- Spot-check: mutation methods must stay `HttpMethod::Post`.
- `429` is in client `retryableStatuses`. `FlickrClient::checkRateLimit()` remains the post-retry handler.
- Do **not** wire `withIdempotencyKey()` — Flickr does not document recognizing that header.

**How:** Keep existing `withRetry` wiring; add `withCircuitBreaker(new CircuitBreakerConfig())`.

---

### 3. Proactive Rate Limiter — via `jooservices/client`

**What:** Enable `ClientBuilder::withRateLimit()` with an overridable unofficial ~3600 req/hour default.

**Concurrency honesty:** default store is in-memory (per-process). Shared `Psr16RateLimitStore` is best-effort, not atomic across PHP-FPM workers. Document this; do not claim multi-worker safety.

**No `src/RateLimit/*` namespace.**

---

### 4. Upload/Replace Workflow v2 + Bounded Ticket Polling

**What:**

- Keep `checkTickets()` as a single instant call.
- Add opt-in bounded `TicketPoller` (blocking; CLI/queue/cron only).
- Client-side upload size cap, fetched once and cached (not per upload).

**State machine:** do **not** add `jooservices/state-machine`. Use plain `TicketStatus` enum + loop. Revisit only if persisted multi-workflow state appears.

**How:** `src/Upload/TicketStatus.php`, `src/Upload/TicketPoller.php`; hydrate `UploadTicketData`; cache limits via existing cache adapter in `FileValidator`.

Broader typed workflow expansion (photo edit/geo/favorites/…) is a **post-v2 follow-up**, not part of this cut.

---

### 5. Public Testing Kit (`FlickrFake`)

**What:** Method-aware fake on top of `jooservices/client` fakes (`ClientBuilder::fake()`, `TestResponse`).

**Existing overlap:** `FakeFlickrTransport` stays as a low-level transport fake. `FlickrFake` is the preferred consumer API.

---

### 6. Method Registry Introspection + Typo Guidance

**What:**

- `Flickr::describe()` returns a **public** readonly DTO (do not leak `@internal` `FlickrMethodDefinition`).
- Levenshtein suggestion (distance ≤ 3) only in failure exception messages; keep permissive registry fallback for unknown/future methods.

---

## Superseded Ideas And Why

- **Custom `TokenBucketRateLimiter` on `psr/simple-cache`:** superseded by Feature 3 (PSR-16 has no atomic CAS).
- **New "idempotent" flag on all 224 registry entries:** superseded by Feature 2 (client excludes POST by default).
- **Reimplementing transport fakes from scratch:** superseded by Feature 5 (build on client fakes).
- **Lazy Objects for `FlickrFactory`:** dropped — micro-benchmark showed ~8.75µs; PHP `>=8.5` floor matches client 2.0's requirement.
- **`withIdempotencyKey()` for Flickr:** not adopted — no evidence Flickr honors the header.
- **`RateLimitException` "quota remaining" field:** rejected — `flickr.people.getLimits` returns upload limits, not request quota.
- **`jooservices/state-machine` for ticket polling:** rejected — in-process enum is enough; package also lacks a stable Packagist tag as of planning re-check.

---

## Performance / Security / DX Plan

**Performance:** retry/CB/rate-limit owned by client config; public GET cache only; lazy pagination; measure raw-path overhead before claiming wins.

**Security (already done — do not re-plan):**

- `FileTokenStore` `chmod(0600)`
- `EncryptedTokenStore` + configuration docs
- `AuthorizationException` / `RateLimitException` wired
- `composer audit` + Dependabot in CI

**New for v2:** upload size cap; structured exception metadata; migration guide; deprecation policy; consumer smoke test; public testing kit; registry describe + typo hints.

---

## Breaking Changes Policy

- Break only where correctness, safety, or long-term maintainability improves.
- Every break needs changelog entry, migration note, and replacement pattern.

## Delivery Phases

### Phase 0: Fix What's Already Broken, Verify Assumptions

- Correct this roadmap and align CHANGELOG to **v2.0.0** (was incorrectly labeled v1.2.0).
- Lock client 2.0 + dto 1.3; require exceptions `^0.5`.
- Spot-check registry `HttpMethod` for mutations.
- Document rate-limit store concurrency limits.

### Phase 1: Foundation

- Exception model on `jooservices/exceptions` with structured fields.

### Phase 2: Core Reliability

- Wire circuit breaker + rate limiter through client.

### Phase 3: Upload

- Bounded ticket poller + cached upload size cap.

### Phase 4: DX

- `FlickrFake`, `describe()`, typo suggestions.

### Phase 5: Release Readiness

- Migration guide, deprecation policy, CI smoke test, examples, full `composer ci`, RC/tag.

## Exit Criteria For v2.0.0

- Dependencies locked to client 2.x, dto 1.3.x, exceptions 0.5.x.
- Exception strategy unified on `jooservices/exceptions` with structured Flickr metadata.
- Retry, circuit breaker, and rate limiting configured through client (not reimplemented).
- Ticket polling is opt-in/blocking with hard timeout and minimum poll interval.
- Public Testing Kit shipped and dogfooded where practical.
- Migration guide + deprecation policy + CHANGELOG complete.
