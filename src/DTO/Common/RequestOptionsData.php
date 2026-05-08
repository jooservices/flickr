<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Common;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Enums\CachePolicy;

final class RequestOptionsData extends Dto
{
    public function __construct(
        public bool $authenticated = false,
        public CachePolicy $cache = CachePolicy::Default,
        public ?int $cacheTtl = null,
        public bool $throwOnApiError = false,
    ) {}
}
