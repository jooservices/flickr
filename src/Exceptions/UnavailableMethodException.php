<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Exceptions;

final class UnavailableMethodException extends ApiException
{
    public function __construct(string $method, ?string $reason = null)
    {
        parent::__construct(
            sprintf('Flickr method "%s" is unavailable%s.', $method, $reason === null ? '' : ': ' . $reason),
            flickrCode: 'unavailable-method',
            retryable: false,
        );
    }
}
