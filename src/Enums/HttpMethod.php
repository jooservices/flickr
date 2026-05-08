<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
}
