<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Exceptions;

/**
 * Flickr REST error-code helpers.
 *
 * Only codes confirmed against official Flickr docs belong here — do not guess.
 */
final class FlickrErrorCodeMap
{
    /** @var list<int> */
    private const AUTH_CODES = [96, 97, 98, 99];

    /** @var list<int> */
    private const RETRYABLE_CODES = [105]; // Service currently unavailable

    public static function isAuthCode(int $code): bool
    {
        return in_array($code, self::AUTH_CODES, true);
    }

    public static function isRetryable(int $code): bool
    {
        return in_array($code, self::RETRYABLE_CODES, true);
    }
}
