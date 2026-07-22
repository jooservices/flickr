<?php

declare(strict_types=1);

/**
 * Example: bounded upload ticket poller (CLI / worker only).
 *
 * Usage:
 *   php examples/poll-upload-ticket.php ticket-id
 */

require __DIR__.'/../vendor/autoload.php';

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Upload\TicketStatus;

$ticketId = $argv[1] ?? '';
if ($ticketId === '') {
    fwrite(STDERR, "Usage: php examples/poll-upload-ticket.php <ticket-id>\n");
    exit(1);
}

$flickr = FlickrFactory::make(new FlickrConfig(
    getenv('FLICKR_API_KEY') ?: 'key',
    getenv('FLICKR_API_SECRET') ?: 'secret',
));

$outcome = $flickr->uploads()->ticketPoller()->waitForCompletion($ticketId);

echo match ($outcome->status) {
    TicketStatus::Completed => "Completed photo_id={$outcome->photoId}\n",
    TicketStatus::Failed, TicketStatus::Invalid => "Failed\n",
    TicketStatus::TimedOut => "Timed out\n",
    default => "Status={$outcome->status->name}\n",
};
