<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\GroupServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class GroupService extends AbstractRawService implements GroupServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function join(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.join', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function joinRequest(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.joinRequest', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function leave(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.leave', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function search(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.search', $parameters);
    }
}
