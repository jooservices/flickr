<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Hydrators;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Upload\UploadTicketData;

/**
 * @internal
 */
final class UploadTicketHydrator
{
    /**
     * @return list<UploadTicketData>
     */
    public function fromResponse(ApiResponseData $response): array
    {
        $tickets = $this->ticketRows($response->data);
        $result = [];

        foreach ($tickets as $ticket) {
            $mapped = $this->mapTicket($ticket);
            if ($mapped !== null) {
                $result[] = $mapped;
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return list<array<string, mixed>>
     */
    private function ticketRows(array $data): array
    {
        $uploader = $data['uploader'] ?? $data;
        $tickets = is_array($uploader) ? ($uploader['ticket'] ?? []) : [];

        if ($tickets === [] || ! is_array($tickets)) {
            return [];
        }

        if ($this->isAssoc($tickets)) {
            return [$tickets];
        }

        $list = [];
        foreach ($tickets as $ticket) {
            if (is_array($ticket)) {
                $list[] = $ticket;
            }
        }

        return $list;
    }

    /**
     * @param  array<string, mixed>  $ticket
     */
    private function mapTicket(array $ticket): ?UploadTicketData
    {
        $attrs = is_array($ticket['@attributes'] ?? null) ? $ticket['@attributes'] : $ticket;
        $id = (string) ($attrs['id'] ?? $ticket['id'] ?? '');
        if ($id === '') {
            return null;
        }

        return new UploadTicketData(
            id: $id,
            photoId: $this->photoIdFrom($attrs),
            complete: isset($attrs['complete']) ? (int) $attrs['complete'] : null,
            invalid: $this->invalidFrom($attrs),
        );
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function photoIdFrom(array $attrs): ?string
    {
        $photoId = $attrs['photoid'] ?? $attrs['photo_id'] ?? null;
        if ($photoId === null || $photoId === '') {
            return null;
        }

        return (string) $photoId;
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private function invalidFrom(array $attrs): ?bool
    {
        if (! isset($attrs['invalid'])) {
            return null;
        }

        return (string) $attrs['invalid'] === '1' || $attrs['invalid'] === true;
    }

    /**
     * @param  array<mixed>  $value
     */
    private function isAssoc(array $value): bool
    {
        return array_keys($value) !== range(0, count($value) - 1);
    }
}
