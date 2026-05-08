<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;

final class InMemoryTokenStore implements FlickrTokenStoreContract
{
    public function __construct(private ?AccessTokenData $token = null) {}

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
