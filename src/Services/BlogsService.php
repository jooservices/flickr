<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\BlogsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class BlogsService extends AbstractRawService implements BlogsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.blogs.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getServices(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.blogs.getServices', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function postPhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.blogs.postPhoto', $parameters);
    }
}
