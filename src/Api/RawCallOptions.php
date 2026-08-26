<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Api;

use InvalidArgumentException;
use JOOservices\Flickr\Enums\AuthenticationMode;

/**
 * Raw calls state their authentication mode explicitly: `Automatic` is
 * rejected and raw responses are never cacheable.
 */
final class RawCallOptions
{
    public readonly AuthenticationMode $mode;

    public function __construct(
        AuthenticationMode $mode,
        public readonly bool $throwOnApiError = false,
    ) {
        if ($mode === AuthenticationMode::Automatic) {
            throw new InvalidArgumentException('Raw calls require an explicit authenticated or unauthenticated mode.');
        }

        $this->mode = $mode;
    }
}
