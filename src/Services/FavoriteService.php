<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\FavoriteServiceContract;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;
use JOOservices\Flickr\DTO\Favorites\FavoritePhotoData;
use JOOservices\Flickr\Hydrators\FavoriteHydrator;
use JOOservices\Flickr\Pagination\Paginator;

final class FavoriteService extends AbstractRawService implements FavoriteServiceContract
{
    public function __construct(
        RawApiServiceContract $raw,
        private FavoriteHydrator $hydrator = new FavoriteHydrator,
        private Paginator $paginator = new Paginator,
    ) {
        parent::__construct($raw);
    }

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
     * @return list<FavoritePhotoData>
     */
    public function getListData(array $parameters = []): array
    {
        return $this->hydrator->list($this->getList($parameters));
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return iterable<ApiResponseData>
     */
    public function getListPages(array $parameters = [], ?PaginationOptionsData $pagination = null, ?RequestOptionsData $requestOptions = null): iterable
    {
        return $this->paginator->pages(
            fn (int $page, ?int $perPage): ApiResponseData => $this->raw->call(
                'flickr.favorites.getList',
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
