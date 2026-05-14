<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Common;

use InvalidArgumentException;
use JOOservices\Dto\Core\Dto;

final class PaginationOptionsData extends Dto
{
    public function __construct(
        public ?int $maxPages = null,
        public ?int $perPage = null,
        public int $startPage = 1,
        public bool $stopWhenEmpty = true,
    ) {
        if ($this->maxPages !== null && $this->maxPages < 1) {
            throw new InvalidArgumentException('Max pages must be at least 1.');
        }

        if ($this->perPage !== null && $this->perPage < 1) {
            throw new InvalidArgumentException('Per page must be at least 1.');
        }

        if ($this->startPage < 1) {
            throw new InvalidArgumentException('Start page must be at least 1.');
        }
    }
}
