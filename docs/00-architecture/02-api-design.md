# API Design

The root `Flickr` object exposes `raw()`, `auth()`, `tokens()`, `photos()`, `photosets()`, `people()`, `groups()`, `favorites()`, `galleries()`, `tags()`, and `uploads()`.

The SDK implements raw calls, OAuth, upload, replace, async ticket checks, and service wrappers for every method currently listed in Flickr's official API method index.

Typed convenience wrappers are focused on the highest-use areas:

- **Request DTOs** for search, upload, replace, and photoset creation
- **Response hydrators** (`*Data()` methods) for priority photo, people, photoset, favorite, group pool, tag, and place workflows
- **Pagination helpers** via `Paginator` for search, people photos, and favorites lists
- **Utilities** such as `PhotoUrlBuilder` for Flickr image URLs

Other wrappers accept an associative parameter array and return `ApiResponseData` so the full API surface stays available without inventing request DTO schemas for every method.

Raw fallback remains available for unknown or future Flickr methods.
