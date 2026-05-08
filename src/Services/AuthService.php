<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\AuthServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class AuthService extends AbstractRawService implements AuthServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function checkToken(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.auth.checkToken', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getFrob(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.auth.getFrob', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getFullToken(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.auth.getFullToken', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getToken(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.auth.getToken', $parameters);
    }
}
