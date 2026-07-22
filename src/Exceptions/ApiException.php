<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Exceptions;

use Throwable;

class ApiException extends FlickrException
{
    public function __construct(
        string $message = '',
        private ?int $apiCode = null,
        int $code = 0,
        ?Throwable $previous = null,
        private ?int $httpStatus = null,
        private bool $retryable = false,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function apiCode(): ?int
    {
        return $this->apiCode;
    }

    public function httpStatus(): ?int
    {
        return $this->httpStatus;
    }

    public function retryable(): bool
    {
        return $this->retryable;
    }
}
