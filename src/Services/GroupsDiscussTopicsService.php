<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\GroupsDiscussTopicsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class GroupsDiscussTopicsService extends AbstractRawService implements GroupsDiscussTopicsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function add(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.topics.add', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.topics.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.topics.getList', $parameters);
    }
}
