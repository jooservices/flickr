<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\ActivityServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class ActivityService extends AbstractRawService implements ActivityServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function userComments(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.activity.userComments', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function userPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.activity.userPhotos', $parameters);
    }
}
