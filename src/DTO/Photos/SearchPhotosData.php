<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Enums\License;
use JOOservices\Flickr\Enums\PhotoExtra;
use JOOservices\Flickr\Enums\PrivacyFilter;
use JOOservices\Flickr\Enums\SortOrder;

final class SearchPhotosData extends Dto
{
    /**
     * @param  list<string>  $tags
     * @param  list<PhotoExtra|string>  $extras
     * @param  array<string, mixed>  $extraParameters
     */
    public function __construct(
        public ?string $text = null,
        public array $tags = [],
        public ?string $userId = null,
        public array $extras = [],
        public int $page = 1,
        public int $perPage = 100,
        public SortOrder|string|null $sort = null,
        public ?string $tagMode = null,
        public ?int $safeSearch = null,
        public ?License $license = null,
        public ?PrivacyFilter $privacyFilter = null,
        public array $extraParameters = [],
    ) {}
}
