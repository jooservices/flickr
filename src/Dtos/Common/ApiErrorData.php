<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Common;

final class ApiErrorData
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
    ) {
    }
}
