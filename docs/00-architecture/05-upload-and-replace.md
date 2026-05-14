# Upload And Replace

Upload and replace use dedicated endpoints on `https://up.flickr.com/services/upload` and `https://up.flickr.com/services/replace`.

These flows are intentionally separate from normal REST API calls because Flickr sends binary files. The `photo` multipart field is excluded from OAuth signature generation; all other POST parameters are signed.

The multipart request uses a readable file stream for the `photo` part and closes the stream after the transport request completes.

Async upload or replace sends `async=1` and returns a ticket id. Poll ticket ids with `flickr.photos.upload.checkTickets`.
