<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PushApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getSubscriptions(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.push.getSubscriptions', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTopics(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.push.getTopics', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function subscribe(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.push.subscribe', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function unsubscribe(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.push.unsubscribe', $parameters, $options);
    }
}
