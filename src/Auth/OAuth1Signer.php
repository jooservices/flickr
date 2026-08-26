<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\Signer;

/**
 * HMAC-SHA1 is quarantined here because Flickr's OAuth 1.0a provider
 * mandates it; it is not a general-purpose signing facility.
 */
final class OAuth1Signer implements Signer
{
    public function sign(string $baseString, string $signingKey): string
    {
        return base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));
    }

    public static function signingKey(string $consumerSecret, ?string $tokenSecret): string
    {
        return rawurlencode($consumerSecret) . '&' . rawurlencode($tokenSecret ?? '');
    }
}
