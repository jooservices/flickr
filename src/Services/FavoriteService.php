<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\FavoriteServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class FavoriteService extends AbstractRawService implements FavoriteServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function add(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.favorites.add', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContext(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.favorites.getContext', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.favorites.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.favorites.getPublicList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function remove(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.favorites.remove', $parameters);
    }
}
