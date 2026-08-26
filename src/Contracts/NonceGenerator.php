<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

/**
 * Must return a fresh, cryptographically random value (at least 128 bits of
 * entropy, e.g. `bin2hex(random_bytes(16))`) on every call. The OAuth 1.0a
 * `oauth_nonce` this feeds is the sole replay-protection mechanism for a
 * signed request; a predictable or reused generator (`mt_rand()`,
 * `uniqid()`) silently defeats it.
 */
interface NonceGenerator
{
    public function generate(): string;
}
