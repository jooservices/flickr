<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum SortOrder: string
{
    case DatePostedDesc = 'date-posted-desc';
    case DatePostedAsc = 'date-posted-asc';
    case DateTakenDesc = 'date-taken-desc';
    case DateTakenAsc = 'date-taken-asc';
    case InterestingnessDesc = 'interestingness-desc';
    case InterestingnessAsc = 'interestingness-asc';
    case Relevance = 'relevance';
}
