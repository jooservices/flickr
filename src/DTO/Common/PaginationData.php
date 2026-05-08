<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Common;

use JOOservices\Dto\Core\Dto;

final class PaginationData extends Dto
{
    public function __construct(
        public int $page,
        public int $pages,
        public int $perPage,
        public int $total,
    ) {}
}
