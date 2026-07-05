<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Services\PeopleServiceContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\People\PersonData;
use JOOservices\Flickr\DTO\Photos\PhotoData;
use JOOservices\Flickr\Hydrators\PeopleHydrator;
use JOOservices\Flickr\Pagination\Paginator;

final class PeopleService extends AbstractRawService implements PeopleServiceContract
{
    public function __construct(
        RawApiServiceContract $raw,
        private PeopleHydrator $hydrator = new PeopleHydrator,
        private Paginator $paginator = new Paginator,
    ) {
        parent::__construct($raw);
    }

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

    public function getInfoData(string $userId): PersonData
    {
        return $this->hydrator->person($this->getInfo($userId));
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
     * @return list<PhotoData>
     */
    public function getPhotosData(array $parameters = []): array
    {
        return $this->hydrator->photos($this->getPhotos($parameters));
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return iterable<ApiResponseData>
     */
    public function getPhotosPages(array $parameters = [], ?PaginationOptionsData $pagination = null, ?RequestOptionsData $requestOptions = null): iterable
    {
        return $this->paginator->pages(
            fn (int $page, ?int $perPage): ApiResponseData => $this->raw->call(
                'flickr.people.getPhotos',
                array_merge($parameters, ['page' => $page, 'per_page' => $perPage]),
                $requestOptions,
            ),
            $pagination,
            fn (ApiResponseData $response): bool => ($response->data['photos']['photo'] ?? []) === [],
        );
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
