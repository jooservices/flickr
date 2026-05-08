<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\ProfileServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class ProfileService extends AbstractRawService implements ProfileServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getProfile(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.profile.getProfile', $parameters);
    }
}
