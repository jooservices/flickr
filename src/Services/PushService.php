<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PushServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PushService extends AbstractRawService implements PushServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getSubscriptions(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.push.getSubscriptions', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTopics(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.push.getTopics', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function subscribe(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.push.subscribe', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function unsubscribe(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.push.unsubscribe', $parameters);
    }
}
