<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Dtos\Common\ApiErrorData;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Exceptions\InvalidResponseException;
use JOOservices\Flickr\Exceptions\RateLimitException;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use SimpleXMLElement;

/**
 * JSON REST parsing plus upload/replace XML outcomes. General XML REST is
 * intentionally not supported.
 */
final class FlickrResponseParser
{
    private const BOM = "\xEF\xBB\xBF";
    private const SNIPPET_BYTES = 120;

    public function __construct(private readonly SensitiveDataRedactor $redactor)
    {
    }

    public function parse(RawResponseData $response): ApiResponseData
    {
        if ($response->status === 429) {
            throw new RateLimitException('Flickr rate limit reached.', self::retryAfterSeconds($response));
        }

        $this->assertSuccessfulStatus($response);

        $decoded = json_decode(ltrim($response->body, self::BOM), true);

        if (json_last_error() !== JSON_ERROR_NONE || is_array($decoded) === false || $decoded === []) {
            throw $this->invalidBody($response, 'not valid JSON');
        }

        $stat = $decoded['stat'] ?? null;

        if ($stat === null || is_string($stat) === false) {
            throw $this->invalidBody($response, 'missing or invalid `stat`');
        }

        unset($decoded['stat']);

        if ($stat !== 'ok') {
            return new ApiResponseData(
                false,
                [],
                new ApiErrorData(
                    $this->redactor->redactText(self::scalarToString($decoded['code'] ?? 'unknown')),
                    $this->redactor->redactText(self::scalarToString($decoded['message'] ?? 'Unknown Flickr error')),
                ),
            );
        }

        /** @var array<string, mixed> $decoded */
        return new ApiResponseData(true, $decoded);
    }

    private static function scalarToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        return is_int($value) || is_float($value) ? (string) $value : '';
    }

    public static function retryAfterSeconds(RawResponseData $response): ?int
    {
        $header = $response->header('Retry-After');

        if ($header === null || preg_match('/^\s*\d+\s*$/', $header) !== 1) {
            return null;
        }

        return (int) trim($header);
    }

    private function invalidBody(RawResponseData $response, string $reason): InvalidResponseException
    {
        return new InvalidResponseException(sprintf(
            'Flickr response (HTTP %d) is %s: "%s".',
            $response->status,
            $reason,
            substr($this->redactor->redactText($response->body), 0, self::SNIPPET_BYTES),
        ));
    }

    /**
     * Parses an upload/replace `rsp` XML document into a photo id or ticket
     * ids. DOCTYPE/entity declarations are rejected outright.
     *
     * @return array{photoId: ?string, ticketIds: list<string>}
     */
    public function parseUploadXml(RawResponseData $response): array
    {
        if ($response->status === 429) {
            throw new RateLimitException('Flickr rate limit reached.', self::retryAfterSeconds($response));
        }

        $this->assertSuccessfulStatus($response);
        $body = ltrim($response->body, self::BOM);
        self::assertNoForbiddenMarkup($body);

        $xml = self::loadRspDocument($body);
        $stat = self::xmlAttr($xml, 'stat');

        if ($stat === '') {
            throw new InvalidResponseException('Flickr upload response is missing `rsp/@stat`.');
        }

        if ($stat !== 'ok') {
            throw $this->uploadFailure($xml);
        }

        return self::extractOutcome($xml);
    }

    private static function assertNoForbiddenMarkup(string $body): void
    {
        if (stripos($body, '<!doctype') !== false || stripos($body, '<!entity') !== false) {
            throw new InvalidResponseException('Flickr upload response declares forbidden DOCTYPE/entity markup.');
        }
    }

    private function assertSuccessfulStatus(RawResponseData $response): void
    {
        if ($response->status < 200 || $response->status >= 300) {
            throw new InvalidResponseException(sprintf(
                'Flickr response returned unexpected HTTP status %d: "%s".',
                $response->status,
                substr($this->redactor->redactText($response->body), 0, self::SNIPPET_BYTES),
            ));
        }
    }

    private static function loadRspDocument(string $body): SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body, SimpleXMLElement::class, LIBXML_NONET | LIBXML_COMPACT);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($xml === false || $xml->getName() !== 'rsp') {
            throw new InvalidResponseException('Flickr upload response is not a valid `rsp` document.');
        }

        return $xml;
    }

    private function uploadFailure(SimpleXMLElement $xml): UploadException
    {
        $meta = [];
        $errorAttributes = ($xml->err ?? null)?->attributes();

        if ($errorAttributes !== null) {
            foreach ($errorAttributes as $attribute => $value) {
                $meta[$attribute] = (string) $value;
            }
        }

        return new UploadException($this->redactor->redactText(sprintf(
            'Flickr upload failed (%s).',
            implode(', ', array_map(
                static fn(string $key, string $value): string => $key . '=' . $value,
                array_keys($meta),
                $meta,
            )),
        )));
    }

    /**
     * @return array{photoId: ?string, ticketIds: list<string>}
     */
    private static function extractOutcome(SimpleXMLElement $xml): array
    {
        $photoId = self::extractPhotoId($xml);
        $ticketIds = self::extractTicketIds($xml);

        if ($photoId === '' && $ticketIds === []) {
            throw new InvalidResponseException('Flickr upload response contains neither a photo id nor ticket ids.');
        }

        return ['photoId' => $photoId === '' ? null : $photoId, 'ticketIds' => $ticketIds];
    }

    private static function extractPhotoId(SimpleXMLElement $xml): string
    {
        if (isset($xml->photoid)) {
            $fromText = trim((string) $xml->photoid);

            if ($fromText !== '') {
                return $fromText;
            }

            $fromAttribute = self::xmlAttr($xml->photoid, 'id');

            if ($fromAttribute !== '') {
                return $fromAttribute;
            }
        }

        if (isset($xml->photo) === false) {
            return '';
        }

        $fromAttribute = self::xmlAttr($xml->photo, 'id');

        if ($fromAttribute !== '') {
            return $fromAttribute;
        }

        return trim((string) $xml->photo);
    }

    /**
     * @return list<string>
     */
    private static function extractTicketIds(SimpleXMLElement $xml): array
    {
        if (isset($xml->ticketid) === false) {
            return [];
        }

        $ticketIds = [];

        foreach ($xml->ticketid as $ticket) {
            $ticketId = trim((string) $ticket);

            if ($ticketId === '') {
                $ticketId = self::xmlAttr($ticket, 'id');
            }

            if ($ticketId !== '') {
                $ticketIds[] = $ticketId;
            }
        }

        return $ticketIds;
    }

    private static function xmlAttr(SimpleXMLElement $node, string $name): string
    {
        $attributes = $node->attributes();

        if ($attributes === null) {
            return '';
        }

        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeName === $name) {
                return (string) $attributeValue;
            }
        }

        return '';
    }
}
