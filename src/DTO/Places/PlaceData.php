<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Places;

use JOOservices\Dto\Core\Dto;

final class PlaceData extends Dto
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public string $placeId,
        public ?string $name = null,
        public array $attributes = [],
    ) {}
}
