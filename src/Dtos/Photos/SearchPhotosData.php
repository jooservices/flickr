<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

use InvalidArgumentException;

/**
 * Typed `flickr.photos.search` input. Flickr documents a hard cap of 4,000
 * results per query regardless of pagination.
 */
final class SearchPhotosData
{
    public function __construct(
        public readonly ?string $text = null,
        public readonly ?string $tags = null,
        public readonly ?string $userId = null,
        public readonly ?string $extras = null,
        public readonly int $perPage = 30,
        public readonly int $page = 1,
    ) {
        if ($this->perPage < 1 || $this->perPage > 500) {
            throw new InvalidArgumentException('Per-page must be between 1 and 500.');
        }

        if ($this->page < 1) {
            throw new InvalidArgumentException('Page must be at least 1.');
        }
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'tags' => $this->tags,
            'user_id' => $this->userId,
            'extras' => $this->extras,
            'per_page' => $this->perPage,
            'page' => $this->page,
        ];
    }
}
