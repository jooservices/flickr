<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\ReflectionServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class ReflectionService extends AbstractRawService implements ReflectionServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getMethodInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.reflection.getMethodInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getMethods(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.reflection.getMethods', $parameters);
    }
}
