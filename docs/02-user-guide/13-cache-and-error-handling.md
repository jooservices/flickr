# Cache And Error Handling

JSON REST responses are the primary supported response format. Upload and replace responses are XML because Flickr's binary upload endpoint returns XML.

Normal API calls return `ApiResponseData`. When Flickr returns `stat=fail`, the response has `ok=false` and an `ApiErrorData` value unless request options ask the SDK to throw.

Malformed JSON, malformed XML, empty bodies, and structurally invalid responses throw `InvalidResponseException`. Transport failures throw `TransportException`. Rate-limit and authorization failures throw `RateLimitException` and `AuthorizationException` when Flickr or the transport signals those conditions.

V1 ships cache contracts and adapters:

- `NullCache`
- `Psr16Cache`
- `CacheKeyResolver`

Raw HTTP caching is disabled by default. `FlickrFactory` uses `NullCache` unless a cache adapter is passed.

When a cache adapter is passed, caching is limited to public cacheable GET REST calls. Cache bypasses apply to:

- auth and OAuth methods
- upload, replace, and upload ticket polling
- POST, write, delete, and mutation methods
- auth-required methods and request options with `authenticated=true`
- Flickr `stat=fail` responses

Use `RequestOptionsData(cache: CachePolicy::Disabled)` to bypass cache for an individual call.
