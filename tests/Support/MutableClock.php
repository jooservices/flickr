<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Support;

use JOOservices\Flickr\Contracts\Clock;

final class MutableClock implements Clock
{
    public function __construct(public int $now = 1_700_000_000)
    {
    }

    public function now(): int
    {
        return $this->now;
    }

    public function advance(int $seconds): void
    {
        $this->now += $seconds;
    }
}
