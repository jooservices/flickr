<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Contracts\Client\FlickrTransportContract;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use RuntimeException;

final class FakeFlickrTransport implements FlickrTransportContract
{
    /**
     * @var list<RawResponseData>
     */
    private array $responses = [];

    /**
     * @var list<array{method: string, url: string, options: array<string, mixed>}>
     */
    private array $requests = [];

    /**
     * @param  list<RawResponseData>  $responses
     */
    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    public static function new(): self
    {
        return new self;
    }

    /**
     * @return $this
     */
    public function push(string $body, int $statusCode = 200): self
    {
        $this->responses[] = new RawResponseData($statusCode, $body);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return $this
     */
    public function pushJson(array $data, int $statusCode = 200): self
    {
        return $this->push(json_encode($data, JSON_THROW_ON_ERROR), $statusCode);
    }

    /**
     * @return $this
     */
    public function pushUploadPhotoId(string $photoId, ?string $secret = null, int $statusCode = 200): self
    {
        $secretAttribute = $secret === null ? '' : ' secret="'.htmlspecialchars($secret, ENT_QUOTES).'"';

        return $this->push('<rsp stat="ok"><photoid'.$secretAttribute.'>'.$photoId.'</photoid></rsp>', $statusCode);
    }

    /**
     * @return $this
     */
    public function pushUploadTicket(string $ticketId, int $statusCode = 200): self
    {
        return $this->push('<rsp stat="ok"><ticketid>'.$ticketId.'</ticketid></rsp>', $statusCode);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function request(string $method, string $url, array $options = []): RawResponseData
    {
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'options' => $options,
        ];

        return array_shift($this->responses) ?? new RawResponseData(200, '{"stat":"ok"}');
    }

    /**
     * @return list<array{method: string, url: string, options: array<string, mixed>}>
     */
    public function sentRequests(): array
    {
        return $this->requests;
    }

    /**
     * @return array{method: string, url: string, options: array<string, mixed>}
     */
    public function lastRequest(): array
    {
        if ($this->requests === []) {
            throw new RuntimeException('No Flickr requests were sent.');
        }

        return $this->requests[count($this->requests) - 1];
    }

    public function assertSentMethod(string $flickrMethod): void
    {
        foreach ($this->requests as $request) {
            $parameters = $request['options']['query'] ?? $request['options']['form_params'] ?? [];

            if (($parameters['method'] ?? null) === $flickrMethod) {
                return;
            }
        }

        throw new RuntimeException("Expected Flickr method [{$flickrMethod}] was not sent.");
    }

    public function assertLastRequestIsMultipart(): void
    {
        $request = $this->lastRequest();

        if (! isset($request['options']['multipart']) || ! is_array($request['options']['multipart'])) {
            throw new RuntimeException('Expected the last Flickr request to be multipart.');
        }
    }
}
