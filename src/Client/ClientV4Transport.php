<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Client\Client\HttpClient;
use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Client\Exceptions\DownloadSizeExceededException;
use JOOservices\Client\Response\Response;
use JOOservices\Flickr\Contracts\FlickrTransport;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JOOservices\Flickr\Exceptions\TransportException;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use Psr\Http\Message\RequestInterface;
use Throwable;

/**
 * The only adapter between this package and jooservices/client v4.
 * Redirects are disabled for every Flickr request so signed credentials can
 * never follow an application redirect to a different host.
 */
final class ClientV4Transport implements FlickrTransport
{
    private const SAFE_CONTEXT_BYTES = 160;

    public function __construct(
        private readonly HttpClient $client,
        private readonly SensitiveDataRedactor $redactor,
    ) {
    }

    public function send(RequestInterface $request, RequestOptions $options): RawResponseData
    {
        try {
            return $this->dispatch($request, $options);
        } catch (\JOOservices\Client\Exceptions\InvalidConfigurationException $error) {
            if (ClientBuilder::isFaked() && str_contains($error->getMessage(), 'allowRedirects')) {
                // The client-v4 fake never performs network I/O and cannot
                // model per-request capabilities. This fallback is therefore
                // deliberately limited to its explicit global fake mode.
                return $this->dispatch($request, new RequestOptions());
            }

            throw $error;
        }
    }

    private function dispatch(RequestInterface $request, RequestOptions $options): RawResponseData
    {
        try {
            $psrResponse = $this->client->send($request, $options);
        } catch (DownloadSizeExceededException $error) {
            throw new TransportException('Flickr response exceeded the allowed body size.', previous: $this->safePrevious($error));
        } catch (\JOOservices\Client\Exceptions\InvalidConfigurationException $error) {
            throw $error;
        } catch (\Throwable $error) {
            if ($error instanceof \JOOservices\Flickr\Exceptions\FlickrException) {
                throw $error;
            }

            throw new TransportException(
                $this->redactor->redactText($error->getMessage()),
                previous: $this->safePrevious($error),
            );
        }

        return $this->materialize($psrResponse);
    }

    /**
     * Callers sometimes log a full exception chain. The previous throwable
     * may originate from an HTTP client whose message (or trace arguments)
     * embed the request URI, which can carry signed OAuth credentials for
     * GET requests. Replace it with a redacted stand-in that keeps the
     * original class name for diagnostics without repeating the raw text.
     */
    private function safePrevious(Throwable $error): Throwable
    {
        return new TransportException(sprintf('%s: %s', $error::class, $this->redactor->redactText($error->getMessage())));
    }

    private function materialize(\Psr\Http\Message\ResponseInterface $psrResponse): RawResponseData
    {
        $headers = [];

        foreach ($psrResponse->getHeaders() as $name => $values) {
            $headers[strtolower($name)] = array_values($values);
        }

        try {
            $body = Response::from($psrResponse)->body();
        } catch (DownloadSizeExceededException $error) {
            throw new TransportException('Flickr response exceeded the allowed body size.', previous: $this->safePrevious($error));
        } catch (Throwable $error) {
            throw new InvalidResponseException(
                sprintf(
                    'Unable to read the Flickr response body safely (%s).',
                    substr($this->redactor->redactText($error->getMessage()), 0, self::SAFE_CONTEXT_BYTES),
                ),
                previous: $this->safePrevious($error),
            );
        }

        return new RawResponseData($psrResponse->getStatusCode(), $headers, $body);
    }
}
