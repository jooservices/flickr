<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Contracts\HttpClientInterface;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use JOOservices\Flickr\Exceptions\TransportException;
use Throwable;

final class JooClientTransport implements FlickrTransportContract
{
    public function __construct(private HttpClientInterface $client) {}

    public static function fromConfig(FlickrConfig $config): self
    {
        $builder = ClientBuilder::create()
            ->withTimeout($config->timeoutSeconds)
            ->withHttpErrors(false)
            ->withUserAgent($config->userAgent);

        return new self($builder->build());
    }

    public function request(string $method, string $url, array $options = []): RawResponseData
    {
        try {
            $response = $this->client->request($method, $url, $options);
            $psr = $response->toPsrResponse();

            return new RawResponseData(
                statusCode: $psr->getStatusCode(),
                body: (string) $psr->getBody(),
                headers: $psr->getHeaders(),
            );
        } catch (Throwable $exception) {
            throw new TransportException('Flickr transport request failed: '.$exception->getMessage(), 0, $exception);
        }
    }
}
