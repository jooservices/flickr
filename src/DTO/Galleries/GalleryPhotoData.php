<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Galleries;

use JOOservices\Dto\Core\Dto;

final class GalleryPhotoData extends Dto
{
    public function __construct(public readonly string $id, public readonly ?string $title = null) {}
}
