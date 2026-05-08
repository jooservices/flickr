<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\TestServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class TestService extends AbstractRawService implements TestServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function echo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.test.echo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function login(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.test.login', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function null(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.test.null', $parameters);
    }
}
