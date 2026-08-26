<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\TokenStore;

final class NullTokenStore implements TokenStore
{
    public function get(): ?AccessTokenData
    {
        return null;
    }

    public function put(AccessTokenData $token): void
    {
    }

    public function forget(): void
    {
    }
}
