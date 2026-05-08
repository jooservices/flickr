<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosTransformServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosTransformService extends AbstractRawService implements PhotosTransformServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function rotate(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.transform.rotate', $parameters);
    }
}
