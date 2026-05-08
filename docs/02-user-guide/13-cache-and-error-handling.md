# Cache And Error Handling

JSON REST responses are the primary supported response format. Upload and replace responses are XML because Flickr's binary upload endpoint returns XML.

Normal API calls return `ApiResponseData`. When Flickr returns `stat=fail`, the response has `ok=false` and an `ApiErrorData` value unless request options ask the SDK to throw.

Malformed JSON, malformed XML, empty bodies, and structurally invalid responses throw `InvalidResponseException`. Transport failures throw `TransportException`.

V1 ships cache contracts and adapters:

- `NullCache`
- `Psr16Cache`
- `CacheKeyResolver`

Raw HTTP caching is disabled by default. Mutation, auth, upload, replace, and authenticated private workflows must not be cached by default.
