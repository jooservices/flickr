# Authentication

Flickr OAuth uses OAuth 1.0a and HMAC-SHA1 signatures. The SDK signs OAuth parameter sets with normalized, percent-encoded parameters and stores access tokens through `FlickrTokenStoreContract`.

Available stores:

- `InMemoryTokenStore`
- `FileTokenStore`
- `NullTokenStore`

Request token, authorization URL, and access token exchange follow Flickr's OAuth documentation.
