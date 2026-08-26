<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Api;

use InvalidArgumentException;
use JOOservices\Flickr\Client\ApiClient;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Common\MethodInfo;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Metadata\FlickrMethodDefinition;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;

/**
 * The public universal gateway used by consumers and every domain service.
 */
final class Api
{
    public function __construct(
        private readonly ApiClient $client,
        private readonly FlickrMethodRegistry $registry,
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function call(string $method, array $parameters = [], ?ApiCallOptions $options = null): ApiResponseData
    {
        $definition = $this->registry->find($method);

        if ($definition === null) {
            throw new InvalidArgumentException($this->unknownMethodMessage($method));
        }

        return $this->client->call($definition, $parameters, $options ?? new ApiCallOptions());
    }

    /**
     * Explicit future/undocumented-method fallback: the caller states the
     * HTTP verb and the authentication mode; raw calls are never cached.
     *
     * @param array<string, mixed> $parameters
     */
    public function raw(
        string $method,
        HttpMethod $httpMethod,
        RawCallOptions $options,
        array $parameters = [],
    ): ApiResponseData {
        $definition = new FlickrMethodDefinition(
            name: $method,
            httpMethod: $httpMethod,
            permission: AuthPermission::None,
            cacheable: false,
            available: true,
        );

        return $this->client->call(
            $definition,
            $parameters,
            new ApiCallOptions(mode: $options->mode, bypassCache: true, throwOnApiError: $options->throwOnApiError),
        );
    }

    public function describe(string $method): ?MethodInfo
    {
        $definition = $this->registry->find($method);

        return $definition === null ? null : MethodInfo::fromDefinition($definition);
    }

    private function unknownMethodMessage(string $method): string
    {
        $message = sprintf('Unknown Flickr method "%s".', $method);
        $suggestion = $this->registry->suggest($method);

        return $suggestion === null || $suggestion === $method
            ? $message
            : $message . sprintf(' Did you mean "%s"?', $suggestion);
    }
}
