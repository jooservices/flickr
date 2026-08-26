<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Testing;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Testing\HttpFakeRegistry;
use JOOservices\Client\Testing\RecordedRequest;
use JOOservices\Client\Testing\TestResponseSequence;
use JOOservices\Flickr\Support\QueryString;
use Nyholm\Psr7\Response;
use RuntimeException;

/**
 * Test-only. Semantic convenience layer over client v4 fakes. Responses are
 * consumed in strict order from one shared sequence; assertions inspect
 * recorded requests. POST bodies are never dynamically routed by
 * `flickr method`.
 *
 * @internal `create()` flips `ClientBuilder::fake()` — process-wide, global
 * state in the `jooservices/client` package, not scoped to one `Flickr`
 * instance. Every `Flickr`/`FlickrFactory::make()` call anywhere in the
 * process starts returning queued fake responses instead of hitting the
 * real API until `close()` runs. Never call this from a production
 * bootstrap path; always pair `create()` with `close()` (e.g. in
 * `tearDown()`), including on a failed test, or fake mode leaks into every
 * later request for the rest of the process.
 */
final class FlickrFake
{
    private const QUEUE_PATTERN = '*';

    private ?TestResponseSequence $sequence = null;

    private function __construct(private readonly HttpFakeRegistry $registry)
    {
    }

    public static function create(): self
    {
        return new self(ClientBuilder::fake());
    }

    /**
     * @param array<string, mixed> $json a Flickr JSON body including its `stat`
     */
    public function queueJson(array $json, int $status = 200): self
    {
        return $this->queue(new Response(
            $status,
            ['Content-Type' => 'application/json'],
            (string) json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ));
    }

    public function queueXml(string $body, int $status = 200): self
    {
        return $this->queue(new Response($status, ['Content-Type' => 'text/xml'], $body));
    }

    public function queueRaw(\JOOservices\Flickr\Dtos\Common\RawResponseData $raw): self
    {
        return $this->queue(new Response($raw->status, array_map(
            static fn(array $values): array => $values,
            $raw->headers,
        ), $raw->body));
    }

    public function queueThrowable(\Throwable $error): self
    {
        return $this->queue($error);
    }

    /** @return list<RecordedRequest> */
    public function recorded(): array
    {
        return ClientBuilder::recorded();
    }

    /**
     * Asserts that the recorded request at the given position dispatched the
     * expected Flickr method with the given parameter subset present.
     *
     * @param array<string, string> $expectedSubset
     */
    public function assertCall(int $position, string $method, array $expectedSubset = []): void
    {
        $recorded = $this->recorded();

        if (!isset($recorded[$position])) {
            throw new RuntimeException(sprintf('No recorded request at position %d.', $position));
        }

        $request = $recorded[$position]->request;
        $parameters = $request->getMethod() === 'GET'
            ? QueryString::parseForm($request->getUri()->getQuery())
            : QueryString::parseForm((string) $request->getBody());

        $dispatched = $parameters['method'] ?? null;

        if ($dispatched !== $method) {
            throw new RuntimeException(sprintf(
                'Expected request %d to dispatch "%s", got "%s".',
                $position,
                $method,
                is_string($dispatched) ? $dispatched : '(none)',
            ));
        }

        foreach ($expectedSubset as $name => $value) {
            $actual = is_array($parameters[$name] ?? null) ? ($parameters[$name][0] ?? null) : ($parameters[$name] ?? null);

            if ($actual !== $value) {
                throw new RuntimeException(sprintf(
                    'Request %d parameter "%s" was "%s", expected "%s".',
                    $position,
                    $name,
                    is_string($actual) ? $actual : 'null',
                    $value,
                ));
            }
        }
    }

    /** Idempotent teardown safe to call even after a failed test. */
    public function close(): void
    {
        ClientBuilder::clearFake();
    }

    private function queue(Response|\Throwable $response): self
    {
        if ($this->sequence === null) {
            $this->sequence = new TestResponseSequence();
            $this->registry->respond('*', self::QUEUE_PATTERN, $this->sequence);
        }

        $this->sequence->push($response);

        return $this;
    }
}
