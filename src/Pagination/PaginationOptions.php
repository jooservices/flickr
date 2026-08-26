<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Pagination;

use InvalidArgumentException;

final class PaginationOptions
{
    /**
     * A bound on *page count*, not item count — the actual item ceiling
     * scales with `perPage` (10,000 pages is 5,000,000 items at perPage=500,
     * but only 10,000 items at perPage=1). It exists to reject a config
     * mistake like `PHP_INT_MAX`, which combined with `stopWhenEmpty: false`
     * and a response shape the `pages` lookup can't find would otherwise let
     * the paginator hammer the API for an effectively unbounded number of
     * calls; no realistic use case needs more than 10,000 pages.
     */
    private const MAX_PAGES_CEILING = 10_000;

    /**
     * @param list<string> $itemsPath
     */
    public function __construct(
        public readonly int $maxPages,
        public readonly int $perPage = 100,
        public readonly bool $stopWhenEmpty = true,
        public readonly array $itemsPath = ['photos', 'photo'],
    ) {
        if ($this->maxPages < 1 || $this->maxPages > self::MAX_PAGES_CEILING) {
            throw new InvalidArgumentException(sprintf('Pagination must be bounded between 1 and %d pages.', self::MAX_PAGES_CEILING));
        }

        if ($this->perPage < 1 || $this->perPage > 500) {
            throw new InvalidArgumentException('Per-page must be between 1 and 500.');
        }
    }
}
