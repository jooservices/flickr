<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

/**
 * Flickr's classic size-suffix vocabulary, accepted directly by
 * {@see \JOOservices\Flickr\Support\PhotoUrlBuilder::build()}.
 */
enum PhotoSize: string
{
    case Square = 's';
    case Thumbnail = 't';
    case Small = 'm';
    case Medium = '';
    case Medium640 = 'z';
    case Large = 'b';
    case Large1024 = 'k';
    case Original = 'o';
}
