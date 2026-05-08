<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Common;

use JOOservices\Dto\Core\Dto;

final class ApiResponseData extends Dto
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public bool $ok,
        public array $data,
        public ?ApiErrorData $error = null,
        public ?PaginationData $pagination = null,
        public ?RawResponseData $raw = null,
    ) {}
}
