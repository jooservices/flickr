<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface PandaServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotos(array $parameters = []): ApiResponseData;
}
