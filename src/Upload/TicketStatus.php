<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

enum TicketStatus
{
    case Submitted;
    case Polling;
    case Completed;
    case Failed;
    case TimedOut;
    case Invalid;
}
