<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface ContactsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getListRecentlyUploaded(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPublicList(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getTaggingSuggestions(array $parameters = []): ApiResponseData;
}
