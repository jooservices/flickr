<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Flickr\Contracts\Services\RawApiServiceContract;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\RequestOptionsData;

final class FakeRawApiService implements RawApiServiceContract
{
    /**
     * @var list<array{method: string, parameters: array<string, mixed>, options: ?RequestOptionsData}>
     */
    public array $calls = [];

    public function call(string $method, array $parameters = [], ?RequestOptionsData $options = null): ApiResponseData
    {
        $this->calls[] = compact('method', 'parameters', 'options');

        return new ApiResponseData(true, ['stat' => 'ok']);
    }

    /**
     * @return array{method: string, parameters: array<string, mixed>, options: ?RequestOptionsData}
     */
    public function lastCall(): array
    {
        return $this->calls[array_key_last($this->calls)];
    }
}
