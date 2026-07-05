<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\DTO\Common\ApiErrorData;
use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationData;
use JOOservices\Flickr\DTO\Common\RawResponseData;
use JOOservices\Flickr\DTO\Upload\UploadResultData;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JsonException;
use SimpleXMLElement;
use Throwable;

/**
 * @internal
 */
final class FlickrResponseParser
{
    public function parseApi(RawResponseData $raw): ApiResponseData
    {
        $body = trim($raw->body);

        if ($body === '') {
            throw new InvalidResponseException('Flickr returned an empty response (HTTP '.$raw->statusCode.').');
        }

        if (str_starts_with($body, '<')) {
            return $this->parseXmlApi($raw, $body);
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidResponseException('Flickr returned malformed JSON (HTTP '.$raw->statusCode.').', 0, $exception);
        }

        if (! is_array($decoded)) {
            throw new InvalidResponseException('Flickr JSON response must be an object.');
        }

        $stat = $decoded['stat'] ?? null;

        if ($stat === 'fail') {
            return new ApiResponseData(
                ok: false,
                data: $decoded,
                error: new ApiErrorData(
                    code: isset($decoded['code']) ? (int) $decoded['code'] : null,
                    message: (string) ($decoded['message'] ?? 'Flickr API request failed.'),
                ),
                raw: $raw,
            );
        }

        if ($stat !== 'ok') {
            throw new InvalidResponseException('Flickr response is missing a valid stat field.');
        }

        return new ApiResponseData(
            ok: true,
            data: $decoded,
            pagination: $this->pagination($decoded),
            raw: $raw,
        );
    }

    public function parseUpload(RawResponseData $raw): UploadResultData
    {
        $body = trim($raw->body);

        if ($body === '') {
            throw new InvalidResponseException('Flickr returned an empty upload response.');
        }

        try {
            $xml = new SimpleXMLElement($body);
        } catch (Throwable $exception) {
            throw new InvalidResponseException('Flickr returned malformed upload XML.', 0, $exception);
        }

        if ($xml->getName() === 'rsp') {
            $stat = (string) ($xml['stat'] ?? '');

            if ($stat === 'fail') {
                $err = $xml->err;
                throw new InvalidResponseException((string) ($err['msg'] ?? 'Flickr upload failed.'), (int) ($err['code'] ?? 0));
            }

            $photoId = $xml->photoid;
            if ($photoId->count() > 0) {
                return new UploadResultData(
                    photoId: trim((string) $photoId),
                    secret: $this->optionalXmlAttribute($photoId, 'secret'),
                    originalSecret: $this->optionalXmlAttribute($photoId, 'originalsecret'),
                );
            }

            $ticketId = $xml->ticketid;
            if ($ticketId->count() > 0) {
                return new UploadResultData(ticketId: trim((string) $ticketId));
            }
        }

        if ($xml->getName() === 'photoid') {
            return new UploadResultData(
                photoId: trim((string) $xml),
                secret: $this->optionalXmlAttribute($xml, 'secret'),
                originalSecret: $this->optionalXmlAttribute($xml, 'originalsecret'),
            );
        }

        if ($xml->getName() === 'ticketid') {
            return new UploadResultData(ticketId: trim((string) $xml));
        }

        throw new InvalidResponseException('Flickr upload response did not contain a photo id or ticket id.');
    }

    private function parseXmlApi(RawResponseData $raw, string $body): ApiResponseData
    {
        try {
            $xml = new SimpleXMLElement($body);
        } catch (Throwable $exception) {
            throw new InvalidResponseException('Flickr returned malformed XML.', 0, $exception);
        }

        if ($xml->getName() === 'rsp') {
            $stat = (string) ($xml['stat'] ?? '');

            if ($stat === 'fail') {
                $err = $xml->err;
                $parsed = $this->xmlToArray($xml);

                return new ApiResponseData(
                    ok: false,
                    data: $parsed,
                    error: new ApiErrorData(
                        code: isset($err['code']) ? (int) $err['code'] : null,
                        message: (string) ($err['msg'] ?? 'Flickr API request failed.'),
                    ),
                    raw: $raw,
                );
            }

            if ($stat === 'ok') {
                return new ApiResponseData(ok: true, data: $this->xmlToArray($xml), raw: $raw);
            }
        }

        throw new InvalidResponseException('Flickr XML response is missing a valid stat field.');
    }

    private function optionalXmlAttribute(SimpleXMLElement $xml, string $name): ?string
    {
        $value = (string) ($xml[$name] ?? '');

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function pagination(array $data): ?PaginationData
    {
        foreach ($data as $value) {
            if (! is_array($value)) {
                continue;
            }

            if (isset($value['page'], $value['pages'], $value['perpage'], $value['total'])) {
                return new PaginationData(
                    page: (int) $value['page'],
                    pages: (int) $value['pages'],
                    perPage: (int) $value['perpage'],
                    total: (int) $value['total'],
                );
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function xmlToArray(SimpleXMLElement $xml): array
    {
        $json = json_encode($xml, JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }
}
