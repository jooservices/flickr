<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Upload;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Upload\TicketStatus;

final class TicketOutcomeData extends Dto
{
    public function __construct(
        public string $ticketId,
        public TicketStatus $status,
        public ?string $photoId = null,
    ) {}

    public static function timedOut(string $ticketId): self
    {
        return new self($ticketId, TicketStatus::TimedOut);
    }
}
