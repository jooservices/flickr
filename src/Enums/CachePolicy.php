<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum CachePolicy: string
{
    case Default = 'default';
    case Disabled = 'disabled';
    case Enabled = 'enabled';
}
