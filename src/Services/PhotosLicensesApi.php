<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosLicensesApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getAvailable(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.licenses.getAvailable', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getInfo(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.licenses.getInfo', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getLicenseHistory(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.licenses.getLicenseHistory', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function setLicense(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.licenses.setLicense', $parameters, $options);
    }
}
