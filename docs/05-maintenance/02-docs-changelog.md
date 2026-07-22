# Docs Changelog

Initial V1 documentation added for raw API, OAuth, photos, photosets, people, upload, replace, async tickets, testing, and known gaps.

Updated to document full 224-method official Flickr API wrapper coverage.

Updated 2026-05-14 to document method registry verification sources, cache safety expectations, Git flow, CI command alignment, repository metadata guidance, AI contributor workflow, secret handling, and the typed-response expansion roadmap.

Updated 2026-05-14 to document optional public GET caching, lazy photo search pagination, upload stream cleanup, and remaining performance boundaries.

Updated 2026-07-06 for v1.1.0:

- Hydrators and `*Data()` helpers for priority methods.
- `PhotoUrlBuilder`, `Paginator`, search enums, and `EncryptedTokenStore`.
- CI coverage gate, release workflow, registry/API-index verification scripts.
- Full API index at `docs/02-user-guide/12-full-api-index.md`.
- Architecture, testing, release, CI/CD, and maintenance docs aligned with current implementation.

Updated 2026-07-21:

- Added `docs/05-maintenance/03-v2-roadmap.md` for v2.0.0 (corrected against Packagist; implementation in progress).
- Added v1→v2 migration guide and deprecation policy.
- Documented `FlickrFake`, ticket poller, describe(), circuit breaker / rate-limit config.
