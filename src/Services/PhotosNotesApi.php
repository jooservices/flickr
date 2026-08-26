<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosNotesApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function add(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.notes.add', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function delete(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.notes.delete', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function edit(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.notes.edit', $parameters, $options);
    }
}
