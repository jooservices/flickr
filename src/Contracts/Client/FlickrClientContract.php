<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Client;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;

interface FlickrClientContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData;
}
