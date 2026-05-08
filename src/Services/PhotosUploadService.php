<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosUploadServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosUploadService extends AbstractRawService implements PhotosUploadServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function checkTickets(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.upload.checkTickets', $parameters);
    }
}
