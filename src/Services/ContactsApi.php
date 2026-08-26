<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/** Generated from resources/api-surface.php. Do not edit by hand. */
final class ContactsApi extends AbstractApiService
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function getList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.contacts.getList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getListRecentlyUploaded(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.contacts.getListRecentlyUploaded', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getPublicList(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.contacts.getPublicList', $parameters, $options);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function getTaggingSuggestions(array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        return $this->call('flickr.contacts.getTaggingSuggestions', $parameters, $options);
    }
}
