<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use InvalidArgumentException;

final class OAuthCallback
{
    public function __construct(
        public readonly string $token,
        public readonly string $verifier,
    ) {
        if (trim($this->token) === '' || trim($this->verifier) === '') {
            throw new InvalidArgumentException('The OAuth callback token and verifier must not be blank.');
        }
    }
}
