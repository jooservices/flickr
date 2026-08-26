<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Support\NativeSleeper;
use PHPUnit\Framework\TestCase;

final class NativeSleeperTest extends TestCase
{
    public function testSleepsForAtLeastTheRequestedDuration(): void
    {
        $start = microtime(true);
        (new NativeSleeper())->sleep(5);

        self::assertGreaterThanOrEqual(0.005, microtime(true) - $start);
    }

    public function testRejectsNonPositiveDurations(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new NativeSleeper())->sleep(0);
    }
}
