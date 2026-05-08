# API Design

The root `Flickr` object exposes `raw()`, `auth()`, `tokens()`, `photos()`, `photosets()`, `people()`, `groups()`, `favorites()`, `galleries()`, `tags()`, and `uploads()`.

The SDK implements raw calls, OAuth, upload, replace, async ticket checks, and service wrappers for every method currently listed in Flickr's official API method index.

DTO-first convenience wrappers are currently focused on the highest-use areas: photos, people, photosets, upload, replace, and async tickets. Other wrappers accept an associative parameter array so they can expose the full API surface without inventing request DTO schemas.
