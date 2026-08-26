<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\TokenStore;

final class InMemoryTokenStore implements TokenStore
{
    private ?AccessTokenData $token = null;

    public function get(): ?AccessTokenData
    {
        return $this->token;
    }

    public function put(AccessTokenData $token): void
    {
        $this->token = $token;
    }

    public function forget(): void
    {
        $this->token = null;
    }
}
