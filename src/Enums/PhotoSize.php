<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

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
