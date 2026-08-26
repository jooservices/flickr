<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class MachinetagsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getNamespaces(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.machinetags.getNamespaces', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPairs(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.machinetags.getPairs', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPredicates(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.machinetags.getPredicates', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getRecentValues(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.machinetags.getRecentValues', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getValues(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.machinetags.getValues', $parameters, $options);
    }
}
