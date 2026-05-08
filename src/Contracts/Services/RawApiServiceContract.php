<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;

interface RawApiServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData;
}
