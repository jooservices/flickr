<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class CamerasApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getBrandModels(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.cameras.getBrandModels', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getBrands(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.cameras.getBrands', $parameters, $options);
    }
}
