<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\StatsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class StatsService extends AbstractRawService implements StatsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getCSVFiles(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getCSVFiles', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getCollectionDomains(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getCollectionDomains', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getCollectionReferrers(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getCollectionReferrers', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getCollectionStats(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getCollectionStats', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getMostPopularPhotoDateRange(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getMostPopularPhotoDateRange', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotoDomains(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotoDomains', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotoReferrers(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotoReferrers', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotoStats(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotoStats', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotosetDomains(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotosetDomains', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotosetReferrers(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotosetReferrers', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotosetStats(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotosetStats', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotostreamDomains(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotostreamDomains', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotostreamReferrers(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotostreamReferrers', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotostreamStats(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPhotostreamStats', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPopularPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getPopularPhotos', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTotalViews(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.stats.getTotalViews', $parameters);
    }
}
