# Risks, Legacy, And Gaps

Current gaps:

- All official Flickr REST methods have wrappers, but most wrappers accept associative parameter arrays rather than dedicated request DTOs.
- XML normal API support is basic and JSON is the primary supported response format.
- Cache adapters exist, but raw API caching is not wired by default.
- Upload file type validation is intentionally limited to local file safety; Flickr remains the authority for accepted media formats.
