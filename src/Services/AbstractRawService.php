<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Services;

use InvalidArgumentException;
use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;

abstract class AbstractRawService
{
    public function __construct(protected RawApiServiceContract $raw) {}

    /**
     * @param  array<string, mixed>  $parameters
     */
    protected function callRaw(string $method, array $parameters = []): ApiResponseData
    {
        return $this->raw->call($method, $parameters);
    }

    protected function requireId(string $id, string $name): string
    {
        if (trim($id) === '') {
            throw new InvalidArgumentException("Flickr {$name} id is required.");
        }

        return $id;
    }
}
