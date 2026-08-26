<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class LegacyAuthApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function checkToken(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.auth.checkToken', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getFrob(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.auth.getFrob', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getFullToken(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.auth.getFullToken', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getToken(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.auth.getToken', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function oauthCheckToken(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.auth.oauth.checkToken', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getAccessToken(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.auth.oauth.getAccessToken', $parameters, $options);
    }
}
