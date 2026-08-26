<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Support;

use JOOservices\Flickr\Contracts\Sleeper;

final class RecordingSleeper implements Sleeper
{
    /** @var list<int> */
    public array $sleeps = [];

    public function sleep(int $milliseconds): void
    {
        $this->sleeps[] = $milliseconds;
    }
}
