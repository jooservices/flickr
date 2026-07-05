<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum PrivacyFilter: int
{
    case Public = 1;
    case Friends = 2;
    case Family = 3;
    case FriendsAndFamily = 4;
    case Private = 5;
}
