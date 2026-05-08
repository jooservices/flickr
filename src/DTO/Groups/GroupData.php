<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Groups;

use JOOservices\Dto\Core\Dto;

final class GroupData extends Dto
{
    public function __construct(public readonly string $id, public readonly ?string $name = null) {}
}
