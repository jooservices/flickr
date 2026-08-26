<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Dtos\Photos;

final class SearchResultData
{
    /**
     * @param list<PhotoData> $photos
     */
    public function __construct(
        public readonly array $photos,
        public readonly int $page,
        public readonly int $pages,
        public readonly int $perPage,
        public readonly int $total,
    ) {
    }
}
