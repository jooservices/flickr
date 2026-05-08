# Async Upload

Set `async` to `true` on upload or replace. Flickr returns a ticket id instead of a photo id. Poll with:

```php
$flickr->uploads()->checkTickets([$ticketId]);
```
