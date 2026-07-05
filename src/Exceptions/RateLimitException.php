<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Exceptions;

use Throwable;

final class RateLimitException extends ApiException
{
    public function __construct(
        string $message = 'Flickr rate limit exceeded.',
        private ?int $retryAfter = null,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, null, $code, $previous);
    }

    public function retryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
