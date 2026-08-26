<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Exceptions;

final class RateLimitException extends FlickrException
{
    public function __construct(
        string $message,
        public readonly ?int $retryAfterSeconds = null,
    ) {
        parent::__construct($message);
    }
}
