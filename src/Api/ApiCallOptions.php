<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Api;

use JOOservices\Flickr\Enums\AuthenticationMode;

final class ApiCallOptions
{
    public function __construct(
        public readonly AuthenticationMode $mode = AuthenticationMode::Automatic,
        public readonly bool $bypassCache = false,
        public readonly bool $throwOnApiError = false,
    ) {
    }
}
