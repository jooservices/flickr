<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum ResponseFormat: string
{
    case Json = 'json';
    case Xml = 'rest';
}
