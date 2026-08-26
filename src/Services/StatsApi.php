<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class StatsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getCSVFiles(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getCSVFiles', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getCollectionDomains(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getCollectionDomains', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getCollectionReferrers(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getCollectionReferrers', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getCollectionStats(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getCollectionStats', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getMostPopularPhotoDateRange(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getMostPopularPhotoDateRange', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotoDomains(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotoDomains', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotoReferrers(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotoReferrers', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotoStats(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotoStats', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotosetDomains(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotosetDomains', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotosetReferrers(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotosetReferrers', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotosetStats(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotosetStats', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotostreamDomains(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotostreamDomains', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotostreamReferrers(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotostreamReferrers', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPhotostreamStats(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPhotostreamStats', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPopularPhotos(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getPopularPhotos', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTotalViews(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.stats.getTotalViews', $parameters, $options);
    }
}
