<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosPeopleServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosPeopleService extends AbstractRawService implements PhotosPeopleServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function add(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.people.add', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function delete(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.people.delete', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function deleteCoords(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.people.deleteCoords', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editCoords(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.people.editCoords', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.people.getList', $parameters);
    }
}
