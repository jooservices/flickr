<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface ActivityServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function userComments(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function userPhotos(array $parameters = []): ApiResponseData;
}
