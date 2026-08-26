<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Exceptions;

/**
 * Open base for Flickr API-level failures; concrete subclasses (for example
 * UnavailableMethodException) extend it, everything else stays final.
 */
class ApiException extends FlickrException
{
    public function __construct(
        string $message,
        public readonly ?string $flickrCode = null,
        public readonly bool $retryable = false,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
