<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\CollectionsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class CollectionsService extends AbstractRawService implements CollectionsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.collections.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTree(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.collections.getTree', $parameters);
    }
}
