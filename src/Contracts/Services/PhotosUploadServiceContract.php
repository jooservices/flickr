<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface PhotosUploadServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function checkTickets(array $parameters = []): ApiResponseData;
}
