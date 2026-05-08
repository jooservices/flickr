<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Common;

use JOOservices\Dto\Core\Dto;

final class ApiErrorData extends Dto
{
    public function __construct(
        public ?int $code,
        public string $message,
    ) {}
}
