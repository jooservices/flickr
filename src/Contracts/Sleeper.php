<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

interface Sleeper
{
    /** Pauses for the given number of milliseconds; implementations must reject non-positive values. */
    public function sleep(int $milliseconds): void;
}
