<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface AuthOauthServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function checkToken(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAccessToken(array $parameters = []): ApiResponseData;
}
