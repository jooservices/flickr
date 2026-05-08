<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\RawResponseData;

final class FakeTransport implements FlickrTransportContract
{
    /**
     * @var list<array{method: string, url: string, options: array<string, mixed>}>
     */
    public array $requests = [];

    /**
     * @param  list<RawResponseData>  $responses
     */
    public function __construct(private array $responses = []) {}

    public function push(string $body, int $statusCode = 200): void
    {
        $this->responses[] = new RawResponseData($statusCode, $body);
    }

    public function request(string $method, string $url, array $options = []): RawResponseData
    {
        $this->requests[] = compact('method', 'url', 'options');

        return array_shift($this->responses) ?? new RawResponseData(200, '{"stat":"ok"}');
    }

    /**
     * @return array{method: string, url: string, options: array<string, mixed>}
     */
    public function lastRequest(): array
    {
        return $this->requests[array_key_last($this->requests)];
    }
}
