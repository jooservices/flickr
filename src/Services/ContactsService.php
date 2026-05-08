<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\ContactsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class ContactsService extends AbstractRawService implements ContactsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.contacts.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListRecentlyUploaded(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.contacts.getListRecentlyUploaded', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.contacts.getPublicList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTaggingSuggestions(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.contacts.getTaggingSuggestions', $parameters);
    }
}
