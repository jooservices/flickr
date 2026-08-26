<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class Psr17Factories
{
    public function __construct(
        public readonly RequestFactoryInterface $request,
        public readonly StreamFactoryInterface $stream,
        public readonly UriFactoryInterface $uri,
    ) {
    }

    public static function nyholm(): self
    {
        $factory = new Psr17Factory();

        return new self($factory, $factory, $factory);
    }
}
