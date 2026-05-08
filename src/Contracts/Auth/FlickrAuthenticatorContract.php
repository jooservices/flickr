<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Auth;

use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\DTO\Auth\RequestTokenData;
use JOOservices\Flickr\Enums\AuthPermission;

interface FlickrAuthenticatorContract
{
    public function requestToken(AuthPermission $permission = AuthPermission::Read): RequestTokenData;

    public function authorizationUrl(RequestTokenData $requestToken, AuthPermission $permission): string;

    public function accessToken(string $oauthToken, string $oauthVerifier): AccessTokenData;
}
