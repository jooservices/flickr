<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum ContentType: int
{
    case Photo = 1;
    case Screenshot = 2;
    case Other = 3;
}
