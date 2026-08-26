<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosCommentsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function addComment(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.comments.addComment', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function deleteComment(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.comments.deleteComment', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editComment(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.comments.editComment', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.comments.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getRecentForContacts(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photos.comments.getRecentForContacts', $parameters, $options);
    }
}
