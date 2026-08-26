<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Contracts\Sleeper;
use InvalidArgumentException;

final class NativeSleeper implements Sleeper
{
    public function sleep(int $milliseconds): void
    {
        if ($milliseconds <= 0) {
            throw new InvalidArgumentException('Sleep duration must be positive.');
        }

        usleep($milliseconds * 1000);
    }
}
