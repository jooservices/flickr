<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Tags;

use JOOservices\Dto\Core\Dto;

final class TagData extends Dto
{
    public function __construct(public readonly string $value) {}
}
