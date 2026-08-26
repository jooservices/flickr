<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class PhotosetsCommentsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function addComment(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.comments.addComment', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function deleteComment(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.comments.deleteComment', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function editComment(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.comments.editComment', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.photosets.comments.getList', $parameters, $options);
    }
}
