<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Testing;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Testing\TestResponse;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Client\FakeFlickrTransport;
use JOOservices\Flickr\Client\JooClientTransport;
use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\Flickr;
use JOOservices\Flickr\FlickrFactory;
use RuntimeException;

/**
 * Public testing kit for consumers. Built on `jooservices/client` fakes.
 *
 * Prefer this over {@see FakeFlickrTransport} for
 * application tests. The low-level transport fake remains available for
 * transport-layer unit tests.
 */
final class FlickrFake
{
    private function __construct(
        private Flickr $flickr,
        private MethodAwareFlickrFakeTransport $transport,
    ) {}

    public static function create(
        ?FlickrConfig $config = null,
        ?FlickrTokenStoreContract $tokenStore = null,
    ): self {
        ClientBuilder::clearFake();
        ClientBuilder::fake();

        $config ??= new FlickrConfig(
            'test-api-key',
            'test-api-secret',
            enableCircuitBreaker: false,
            enableRateLimit: false,
        );

        $tokenStore ??= new InMemoryTokenStore(new AccessTokenData('test-token', 'test-token-secret'));
        $transport = new MethodAwareFlickrFakeTransport(JooClientTransport::fromConfig($config));

        return new self(
            FlickrFactory::make($config, tokenStore: $tokenStore, transport: $transport),
            $transport,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function respond(string $flickrMethod, array $payload = []): self
    {
        $this->transport->expect($flickrMethod);
        $body = array_key_exists('stat', $payload) ? $payload : array_merge(['stat' => 'ok'], $payload);
        ClientBuilder::push(TestResponse::ok($body));

        return $this;
    }

    public function respondError(string $flickrMethod, int $code, string $message): self
    {
        $this->transport->expect($flickrMethod);
        ClientBuilder::push(TestResponse::ok([
            'stat' => 'fail',
            'code' => $code,
            'message' => $message,
        ]));

        return $this;
    }

    public function flickr(): Flickr
    {
        return $this->flickr;
    }

    /**
     * @param  array<string, mixed>|null  $withParameters
     */
    public function assertCalled(string $flickrMethod, ?array $withParameters = null): void
    {
        foreach ($this->calls($flickrMethod) as $parameters) {
            if ($withParameters === null) {
                return;
            }

            foreach ($withParameters as $key => $value) {
                if (($parameters[$key] ?? null) !== $value) {
                    continue 2;
                }
            }

            return;
        }

        throw new RuntimeException("Expected Flickr method [{$flickrMethod}] was not called.");
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function calls(string $flickrMethod): array
    {
        $matches = [];

        foreach (ClientBuilder::recorded() as $request) {
            $parameters = $this->flickrParameters($request->uri, $request->options, $request->body);
            if (($parameters['method'] ?? null) === $flickrMethod) {
                $matches[] = $parameters;
            }
        }

        return $matches;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function flickrParameters(string $uri, array $options, ?string $body): array
    {
        $parameters = [];

        $query = parse_url($uri, PHP_URL_QUERY);
        if (is_string($query) && $query !== '') {
            $fromUri = [];
            parse_str($query, $fromUri);
            $parameters = $fromUri;
        }

        foreach (['query', 'form_params'] as $key) {
            if (isset($options[$key]) && is_array($options[$key])) {
                $parameters = array_merge($parameters, $options[$key]);
            }
        }

        if ($body !== null && $body !== '' && str_contains($body, '=')) {
            $fromBody = [];
            parse_str($body, $fromBody);
            $parameters = array_merge($parameters, $fromBody);
        }

        return $parameters;
    }
}
