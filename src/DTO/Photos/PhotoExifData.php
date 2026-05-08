<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Photos;

use JOOservices\Dto\Core\Dto;

final class PhotoExifData extends Dto
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(public array $data) {}
}
