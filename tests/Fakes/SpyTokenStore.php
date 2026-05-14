<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;

final class SpyTokenStore implements FlickrTokenStoreContract
{
    public int $getCalls = 0;

    public function __construct(private ?AccessTokenData $token = null) {}

    public function get(): ?AccessTokenData
    {
        $this->getCalls++;

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
