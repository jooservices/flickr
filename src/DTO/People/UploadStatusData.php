<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\People;

use JOOservices\Dto\Core\Dto;

final class UploadStatusData extends Dto
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(public readonly array $attributes = []) {}
}
