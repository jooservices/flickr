# Error Handling

Flickr JSON `stat=ok` maps to `ApiResponseData(ok: true)`.

Flickr JSON or XML `stat=fail` maps to `ApiResponseData(ok: false)` with `ApiErrorData` unless callers request throwing API errors.

Empty, malformed, or structurally invalid responses throw `InvalidResponseException`. HTTP transport failures throw `TransportException`. Rate-limit and authorization failures throw `RateLimitException` and `AuthorizationException` when Flickr or the transport signals those conditions.
