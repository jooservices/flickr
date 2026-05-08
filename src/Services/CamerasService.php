<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\CamerasServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class CamerasService extends AbstractRawService implements CamerasServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getBrandModels(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.cameras.getBrandModels', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getBrands(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.cameras.getBrands', $parameters);
    }
}
