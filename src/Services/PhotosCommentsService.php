<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PhotosCommentsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PhotosCommentsService extends AbstractRawService implements PhotosCommentsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function addComment(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.comments.addComment', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function deleteComment(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.comments.deleteComment', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function editComment(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.comments.editComment', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.comments.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getRecentForContacts(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.photos.comments.getRecentForContacts', $parameters);
    }
}
