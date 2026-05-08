<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum AuthPermission: string
{
    case Read = 'read';
    case Write = 'write';
    case Delete = 'delete';
}
