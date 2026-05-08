<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosLicensesServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosLicensesService extends AbstractRawService implements PhotosLicensesServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getAvailable(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.licenses.getAvailable', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getInfo(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.licenses.getInfo', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getLicenseHistory(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.licenses.getLicenseHistory', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function setLicense(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.licenses.setLicense', $parameters);
    }
}
