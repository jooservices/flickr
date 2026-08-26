<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

use JOOservices\Flickr\Auth\AccessTokenData;

/**
 * Holds exactly one access token. This contract carries no user/tenant
 * identifier, so it is inherently single-token-per-instance: a
 * multi-tenant application must construct one `TokenStore` (and one
 * `Flickr`/`UploadService` instance) per authenticated user, never share a
 * single instance across users — sharing one silently overwrites one
 * user's token with another's on every `put()`.
 */
interface TokenStore
{
    public function get(): ?AccessTokenData;

    public function put(AccessTokenData $token): void;

    public function forget(): void;
}
