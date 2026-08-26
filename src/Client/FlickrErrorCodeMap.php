<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Dtos\Common\ApiErrorData;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Exceptions\AuthenticationException;
use JOOservices\Flickr\Exceptions\AuthorizationException;
use JOOservices\Flickr\Exceptions\FlickrException;

/**
 * Maps Flickr `stat=fail` codes onto typed exceptions when the caller opted
 * into `throwOnApiError`.
 */
final class FlickrErrorCodeMap
{
    /**
     * Flickr's one universal transient failure code, documented on nearly
     * every method as "Service currently unavailable". Every other code is
     * a permanent rejection of this specific request (bad argument, missing
     * resource, disabled feature, ...) and retrying it unchanged would just
     * fail the same way again.
     */
    private const SERVICE_UNAVAILABLE = '105';

    public static function throwFor(ApiErrorData $error): FlickrException
    {
        return match ($error->code) {
            '98' => new AuthenticationException($error->message),
            '99' => new AuthorizationException($error->message),
            default => new ApiException(
                $error->message,
                flickrCode: $error->code,
                retryable: $error->code === self::SERVICE_UNAVAILABLE,
            ),
        };
    }
}
