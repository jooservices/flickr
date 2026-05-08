<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PrefsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PrefsService extends AbstractRawService implements PrefsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getContentType(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.prefs.getContentType', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getGeoPerms(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.prefs.getGeoPerms', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getHidden(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.prefs.getHidden', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPrivacy(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.prefs.getPrivacy', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getSafetyLevel(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.prefs.getSafetyLevel', $parameters);
    }
}
