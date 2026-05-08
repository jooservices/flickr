<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\PandaServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class PandaService extends AbstractRawService implements PandaServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getList(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.panda.getList', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPhotos(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.panda.getPhotos', $parameters);
    }
}
