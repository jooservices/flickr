<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Services;

use JOOservices\Flickr\DTO\Common\ApiResponseData;

interface CamerasServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getBrandModels(array $parameters = []): ApiResponseData;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getBrands(array $parameters = []): ApiResponseData;
}
