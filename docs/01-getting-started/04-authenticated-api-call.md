# Authenticated API Call

```php
$flickr->tokens()->put($accessToken);
$response = $flickr->people()->getUploadStatus();
```

Methods marked as authenticated in the registry require a stored OAuth access token.
