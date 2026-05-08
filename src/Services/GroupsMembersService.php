<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\GroupsMembersServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class GroupsMembersService extends AbstractRawService implements GroupsMembersServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.groups.members.getList', $parameters);
    }
}
