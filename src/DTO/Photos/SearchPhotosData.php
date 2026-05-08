<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;

final class SearchPhotosData extends Dto
{
    /**
     * @param  list<string>  $tags
     * @param  list<string>  $extras
     * @param  array<string, mixed>  $extraParameters
     */
    public function __construct(
        public ?string $text = null,
        public array $tags = [],
        public ?string $userId = null,
        public array $extras = [],
        public int $page = 1,
        public int $perPage = 100,
        public ?string $sort = null,
        public ?string $tagMode = null,
        public ?int $safeSearch = null,
        public array $extraParameters = [],
    ) {}
}
