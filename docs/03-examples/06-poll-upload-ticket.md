# Poll Upload Ticket

Single-shot check:

```php
$tickets = $flickr->uploads()->checkTicketsData(['1234']);
```

Bounded blocking poller (CLI / queue / cron only — never a web request):

```php
$outcome = $flickr->uploads()->ticketPoller()->waitForCompletion(
    ticketId: '1234',
    maxWaitSeconds: 30,
    pollIntervalSeconds: 2,
);

if ($outcome->status === \JOOservices\Flickr\Upload\TicketStatus::Completed) {
    echo $outcome->photoId;
}
```
