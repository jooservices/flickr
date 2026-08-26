<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

final class TicketPollResult
{
    public function __construct(
        public readonly string $ticketId,
        public readonly TicketStatus $status,
        public readonly ?string $photoId = null,
    ) {
    }
}
