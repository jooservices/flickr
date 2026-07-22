<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Contracts\HttpClientInterface;
use JOOservices\Client\Exceptions\NetworkConnectionException;
use JOOservices\Client\Resilience\CircuitBreakerConfig;
use JOOservices\Client\Resilience\Contracts\RateLimitStoreInterface;
use JOOservices\Client\Resilience\RateLimitConfig;
use JOOservices\Client\Resilience\RetryConfig;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use JOOservices\Flickr\Exceptions\TransportException;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use Throwable;

final class JooClientTransport implements FlickrTransportContract
{
    public function __construct(private HttpClientInterface $client) {}

    /**
     * Build transport middleware from config.
     *
     * Rate limiting uses `jooservices/client`'s token bucket. The default store is
     * in-memory and **per-process only**. Pass a shared `RateLimitStoreInterface`
     * (for example `Psr16RateLimitStore`) for multi-worker coordination — that store
     * is best-effort via PSR-16 get/set and is **not** atomically safe across workers.
     */
    public static function fromConfig(
        FlickrConfig $config,
        ?RateLimitStoreInterface $rateLimitStore = null,
    ): self {
        $builder = ClientBuilder::create()
            ->withTimeout($config->timeoutSeconds)
            ->withHttpErrors(false)
            ->withUserAgent($config->userAgent);

        if ($config->retryTimes > 0) {
            $builder->withRetry(new RetryConfig(
                maxAttempts: $config->retryTimes + 1,
                baseDelayMs: 100,
                maxDelayMs: 2000,
                retryableStatuses: [429, 500, 502, 503, 504],
                retryableMethods: ['GET', 'HEAD', 'OPTIONS', 'PUT', 'DELETE'],
                retryableExceptions: [NetworkConnectionException::class],
            ));
        }

        if ($config->enableCircuitBreaker) {
            $builder->withCircuitBreaker(new CircuitBreakerConfig);
        }

        if ($config->enableRateLimit) {
            $builder->withRateLimit(
                new RateLimitConfig(
                    maxTokens: $config->rateLimitMaxTokens,
                    refillRatePerSecond: $config->rateLimitRefillPerSecond,
                ),
                $rateLimitStore,
            );
        }

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
            $message = (new SensitiveDataRedactor)->redact($exception->getMessage());

            throw new TransportException('Flickr transport request failed: '.$message, 0, $exception);
        }
    }
}
