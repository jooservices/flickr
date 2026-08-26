<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum AuthenticationMode: string
{
    case Automatic = 'automatic';
    case Authenticated = 'authenticated';
    case Unauthenticated = 'unauthenticated';
}
