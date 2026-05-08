<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Services\PeopleServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PeopleService extends AbstractRawService implements PeopleServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function findByEmail(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.findByEmail', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function findByUsername(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.findByUsername', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getGroups(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.getGroups', $parameters);
    }

    public function getInfo(string $userId): ApiResponseData
    {
        if (trim($userId) === '') {
            throw new InvalidArgumentException('Flickr user id is required.');
        }

        return $this->callRaw('flickr.people.getInfo', ['user_id' => $userId]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getLimits(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.getLimits', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.getPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotosOf(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.getPhotosOf', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicGroups(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.getPublicGroups', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.people.getPublicPhotos', $parameters);
    }

    public function getUploadStatus(): ApiResponseData
    {
        return $this->callRaw('flickr.people.getUploadStatus');
    }
}
