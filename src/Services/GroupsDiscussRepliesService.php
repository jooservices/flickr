<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\GroupsDiscussRepliesServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class GroupsDiscussRepliesService extends AbstractRawService implements GroupsDiscussRepliesServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function add(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.replies.add', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function delete(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.replies.delete', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function edit(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.replies.edit', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.replies.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.discuss.replies.getList', $parameters);
    }
}
