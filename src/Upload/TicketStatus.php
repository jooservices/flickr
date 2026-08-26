<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

enum TicketStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Invalid = 'invalid';
    case TimedOut = 'timed-out';
}
