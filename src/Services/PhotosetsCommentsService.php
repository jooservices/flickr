<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosetsCommentsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosetsCommentsService extends AbstractRawService implements PhotosetsCommentsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function addComment(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.comments.addComment', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function deleteComment(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.comments.deleteComment', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editComment(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.comments.editComment', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photosets.comments.getList', $parameters);
    }
}
