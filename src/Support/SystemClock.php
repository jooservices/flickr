<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Contracts\Clock;

final class SystemClock implements Clock
{
    public function now(): int
    {
        return time();
    }
}
