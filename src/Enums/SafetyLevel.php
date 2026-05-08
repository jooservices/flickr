<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum SafetyLevel: int
{
    case Safe = 1;
    case Moderate = 2;
    case Restricted = 3;
}
