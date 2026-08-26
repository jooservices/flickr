<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

/**
 * Must return real, monotonically non-decreasing wall-clock Unix seconds.
 * OAuth signing timestamps and `PendingAuthorization`'s expiry window both
 * rely on it; a custom implementation that drifts backward or stalls
 * silently weakens or defeats that expiry check.
 */
interface Clock
{
    public function now(): int;
}
