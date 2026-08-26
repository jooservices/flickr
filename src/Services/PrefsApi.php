<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PrefsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getContentType(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.prefs.getContentType', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getGeoPerms(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.prefs.getGeoPerms', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getHidden(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.prefs.getHidden', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPrivacy(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.prefs.getPrivacy', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getSafetyLevel(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.prefs.getSafetyLevel', $parameters, $options);
    }
}
