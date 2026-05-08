<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Common;

use JOOservices\Dto\Core\Dto;

final class RawResponseData extends Dto
{
    /**
     * @param  array<string, list<string>>  $headers
     */
    public function __construct(
        public int $statusCode,
        public string $body,
        public array $headers = [],
    ) {}
}
