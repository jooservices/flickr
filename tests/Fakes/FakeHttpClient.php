<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Client\Contracts\HttpClientInterface;
use JOOservices\Client\Contracts\ResponseWrapperInterface;
use RuntimeException;

final class FakeHttpClient implements HttpClientInterface
{
    /** @var callable|null */
    private $requestHandler;

    public function __construct(?callable $requestHandler = null)
    {
        $this->requestHandler = $requestHandler;
    }

    public function get(string $uri, array $options = []): ResponseWrapperInterface
    {
        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $options = []): ResponseWrapperInterface
    {
        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $options = []): ResponseWrapperInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    public function patch(string $uri, array $options = []): ResponseWrapperInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    public function delete(string $uri, array $options = []): ResponseWrapperInterface
    {
        return $this->request('DELETE', $uri, $options);
    }

    public function download(string $uri, string $destPath, array $options = []): ResponseWrapperInterface
    {
        return $this->request('GET', $uri, $options);
    }

    public function upload(string $uri, string $filePath, array $fields = [], array $options = []): ResponseWrapperInterface
    {
        return $this->request('POST', $uri, $options);
    }

    public function postJson(string $uri, array $data, array $options = []): ResponseWrapperInterface
    {
        return $this->request('POST', $uri, $options);
    }

    public function putJson(string $uri, array $data, array $options = []): ResponseWrapperInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    public function patchJson(string $uri, array $data, array $options = []): ResponseWrapperInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    public function request(string $method, string $uri, array $options = []): ResponseWrapperInterface
    {
        if ($this->requestHandler !== null) {
            return ($this->requestHandler)($method, $uri, $options);
        }

        throw new RuntimeException('FakeHttpClient has no request handler configured.');
    }
}
