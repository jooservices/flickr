<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;

final class PayloadRawApiService implements RawApiServiceContract
{
    /**
     * @param  array<string, array<string, mixed>>  $payloads
     */
    public function __construct(private array $payloads) {}

    public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
    {
        return new ApiResponseData(true, $this->payloads[$method] ?? ['stat' => 'ok']);
    }
}
