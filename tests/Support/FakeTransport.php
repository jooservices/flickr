<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Support;

use JOOservices\Flickr\Contracts\FlickrTransport;
use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use Psr\Http\Message\RequestInterface;

/**
 * Deterministic in-memory transport: queued outcomes are consumed strictly
 * in order; every dispatched request is captured for assertions.
 */
final class FakeTransport implements FlickrTransport
{
    /** @var list<RawResponseData|\Throwable> */
    private array $queue = [];

    /** @var list<array{0: RequestInterface, 1: RequestOptions}> */
    private array $sent = [];

    /** @var list<string> */
    private array $bodies = [];

    public function queue(RawResponseData|\Throwable ...$outcomes): void
    {
        $this->queue = [...$this->queue, ...array_values($outcomes)];
    }

    public function send(RequestInterface $request, RequestOptions $options): RawResponseData
    {
        $this->bodies[] = (string) $request->getBody();
        $this->sent[] = [$request, $options];

        if ($this->queue === []) {
            throw new \LogicException('FakeTransport received an unexpected request.');
        }

        $next = array_shift($this->queue);

        if ($next instanceof \Throwable) {
            throw $next;
        }

        return $next;
    }

    public function sentCount(): int
    {
        return count($this->sent);
    }

    /**
     * @return list<array{0: RequestInterface, 1: RequestOptions}>
     */
    public function sentRequests(): array
    {
        return $this->sent;
    }

    /** @return list<string> */
    public function sentBodies(): array
    {
        return $this->bodies;
    }
}
