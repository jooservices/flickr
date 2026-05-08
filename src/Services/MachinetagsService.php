<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use JOOservices\Flickr\Contracts\Services\MachinetagsServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

final class MachinetagsService extends AbstractRawService implements MachinetagsServiceContract
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getNamespaces(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.machinetags.getNamespaces', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPairs(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.machinetags.getPairs', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getPredicates(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.machinetags.getPredicates', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getRecentValues(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.machinetags.getRecentValues', $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getValues(array $parameters = []): ApiResponseData
    {
        return $this->callRaw('flickr.machinetags.getValues', $parameters);
    }
}
