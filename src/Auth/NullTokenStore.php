<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;

final class NullTokenStore implements FlickrTokenStoreContract
{
    public function get(): ?AccessTokenData
    {
        return null;
    }

    public function put(AccessTokenData $token): void {}

    public function forget(): void {}
}
