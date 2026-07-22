<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use GuzzleHttp\Psr7\Response;
use JOOservices\Client\Contracts\ResponseWrapperInterface;
use Psr\Http\Message\ResponseInterface;

final class FakeResponseWrapper implements ResponseWrapperInterface
{
    /**
     * @param  array<string, list<string>>  $headers
     */
    public function __construct(
        private int $statusCode = 200,
        private string $body = '',
        private array $headers = [],
    ) {}

    public function status(): int
    {
        return $this->statusCode;
    }

    public function header(string $name): ?string
    {
        foreach ($this->headers as $headerName => $values) {
            if (strcasecmp($headerName, $name) === 0) {
                return $values[0] ?? null;
            }
        }

        return null;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function successful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function failed(): bool
    {
        return ! $this->successful();
    }

    public function clientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function serverError(): bool
    {
        return $this->statusCode >= 500;
    }

    public function json(): array
    {
        $decoded = json_decode($this->body, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function toPsrResponse(): ResponseInterface
    {
        return new Response($this->statusCode, $this->headers, $this->body);
    }

    public function toDto(string $dtoClass): object
    {
        return new $dtoClass;
    }
}
