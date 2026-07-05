<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosUploadServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\Support\ListNormalizer;

final class PhotosUploadService extends AbstractRawService implements PhotosUploadServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     *
     * @deprecated Use UploadService::checkTickets() instead.
     */
    public function checkTickets(array $parameters = []): ApiResponseData
    {
        if (isset($parameters['tickets']) && is_array($parameters['tickets'])) {
            $parameters['tickets'] = ListNormalizer::requireNonEmptyTrimmedList($parameters['tickets'], 'upload ticket id');
        }

        return $this->callRaw('flickr.photos.upload.checkTickets', $parameters);
    }
}
