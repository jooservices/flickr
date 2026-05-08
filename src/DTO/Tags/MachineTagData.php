<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Tags;

use JOOservices\Dto\Core\Dto;

final class MachineTagData extends Dto
{
    public function __construct(
        public readonly string $namespace,
        public readonly string $predicate,
        public readonly string $value,
    ) {}
}
