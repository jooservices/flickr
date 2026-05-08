# Raw API

`raw()->call($method, $parameters)` can call any Flickr method. The SDK adds `method`, `api_key`, `format`, and `nojsoncallback=1` for JSON. Null parameters are removed and arrays are comma-joined.
