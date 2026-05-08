<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\AuthOauthServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class AuthOauthService extends AbstractRawService implements AuthOauthServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function checkToken(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.auth.oauth.checkToken', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAccessToken(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.auth.oauth.getAccessToken', $parameters);
    }
}
