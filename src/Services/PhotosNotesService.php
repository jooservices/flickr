<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosNotesServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosNotesService extends AbstractRawService implements PhotosNotesServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function add(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.notes.add', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function delete(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.notes.delete', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function edit(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.notes.edit', $parameters);
    }
}
