<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\Contracts\Services\TagServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Tags\TagData;
use JOOservices\Flickr\Hydrators\TagHydrator;

final class TagService extends AbstractRawService implements TagServiceContract
{
    public function __construct(
        RawApiServiceContract $raw,
        private TagHydrator $hydrator = new TagHydrator,
    ) {
        parent::__construct($raw);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getClusterPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getClusterPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getClusters(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getClusters', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getHotList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getHotList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return list<TagData>
     */
    public function getHotListData(array $parameters = []): array
    {
        return $this->hydrator->hotList($this->getHotList($parameters));
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListPhoto(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getListPhoto', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListUser(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getListUser', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListUserPopular(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getListUserPopular', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListUserRaw(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getListUserRaw', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getMostFrequentlyUsed(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getMostFrequentlyUsed', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getRelated(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.tags.getRelated', $parameters);
    }
}
