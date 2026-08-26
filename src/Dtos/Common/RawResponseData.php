<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Common;

final class RawResponseData
{
    /**
     * @param array<string, list<string>> $headers lower-cased header names
     */
    public function __construct(
        public readonly int $status,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public function header(string $name): ?string
    {
        $values = $this->headers[strtolower($name)] ?? [];

        return $values[0] ?? null;
    }
}
