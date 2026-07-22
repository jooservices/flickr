<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Testing;

use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use RuntimeException;

/**
 * @internal
 */
final class MethodAwareFlickrFakeTransport implements FlickrTransportContract
{
    /** @var list<string> */
    private array $expectedMethods = [];

    public function __construct(private FlickrTransportContract $inner) {}

    public function expect(string $flickrMethod): void
    {
        $this->expectedMethods[] = $flickrMethod;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function request(string $method, string $url, array $options = []): RawResponseData
    {
        $parameters = $options['query'] ?? $options['form_params'] ?? [];
        $actual = is_array($parameters) ? ($parameters['method'] ?? null) : null;

        if (is_string($actual) && $this->expectedMethods !== []) {
            $expected = array_shift($this->expectedMethods);
            if ($actual !== $expected) {
                throw new RuntimeException("Expected Flickr method [{$expected}], received [{$actual}].");
            }
        }

        return $this->inner->request($method, $url, $options);
    }
}
