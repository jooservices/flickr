<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Auth;

use JOOservices\Flickr\DTO\Auth\AccessTokenData;

interface FlickrTokenStoreContract
{
    public function get(): ?AccessTokenData;

    public function put(AccessTokenData $token): void;

    public function forget(): void;
}
