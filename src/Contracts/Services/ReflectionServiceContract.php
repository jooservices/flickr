<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface ReflectionServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getMethodInfo(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getMethods(array $parameters = []): ApiResponseData;
}
