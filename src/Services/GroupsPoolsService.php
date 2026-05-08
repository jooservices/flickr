<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\GroupsPoolsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class GroupsPoolsService extends AbstractRawService implements GroupsPoolsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function add(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.pools.add', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContext(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.pools.getContext', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getGroups(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.pools.getGroups', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.pools.getPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function remove(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.pools.remove', $parameters);
    }
}
