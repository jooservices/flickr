<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

/**
 * Upload/replace outcome: exactly one of a photo id (sync success) or ticket
 * ids (`async` accepted) — anything else fails before this type exists.
 */
final class UploadResultData
{
    /**
     * @param list<string> $ticketIds
     */
    public function __construct(
        public readonly ?string $photoId,
        public readonly array $ticketIds = [],
        public readonly bool $fromReplace = false,
    ) {
    }
}
