<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Config\FlickrEndpoints;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Support\QueryString;
use Psr\Http\Message\RequestInterface;

/**
 * Builds REST requests against the package-owned endpoint only. GET carries
 * RFC 3986 query parameters; POST carries an application/x-www-form-urlencoded
 * body. No Guzzle-style option arrays exist here.
 */
final class FlickrRequestBuilder
{
    public const FORMAT_PARAMS = ['format' => 'json', 'nojsoncallback' => '1'];

    public function __construct(private readonly Psr17Factories $psr17, private readonly string $userAgent)
    {
    }

    /**
     * @param array<string, string|list<string>> $parameters complete canonical parameter set
     */
    public function rest(HttpMethod $verb, array $parameters): RequestInterface
    {
        if ($verb === HttpMethod::Get) {
            return $this->psr17->request
                ->createRequest(HttpMethod::Get->value, FlickrEndpoints::REST . '?' . QueryString::build($parameters))
                ->withHeader('User-Agent', $this->userAgent);
        }

        return $this->psr17->request
            ->createRequest(HttpMethod::Post->value, FlickrEndpoints::REST)
            ->withBody($this->psr17->stream->createStream(QueryString::build($parameters)))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('User-Agent', $this->userAgent);
    }

    /**
     * POST form to a fixed package-owned endpoint (OAuth token endpoints).
     *
     * @param array<string, string|list<string>> $parameters complete canonical parameter set
     */
    public function formPost(string $uri, array $parameters): RequestInterface
    {
        return $this->psr17->request
            ->createRequest(HttpMethod::Post->value, $uri)
            ->withBody($this->psr17->stream->createStream(QueryString::build($parameters)))
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('User-Agent', $this->userAgent);
    }
}
