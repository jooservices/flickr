<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Upload;

use JOOservices\Dto\Core\Dto;

final class UploadTicketData extends Dto
{
    /**
     * @param  int|null  $complete  Flickr ticket status: 0 = pending, 1 = completed, 2 = failed
     */
    public function __construct(
        public string $id,
        public ?string $photoId = null,
        public ?int $complete = null,
        public ?bool $invalid = null,
    ) {}

    public function isPending(): bool
    {
        return $this->invalid !== true && $this->complete === 0;
    }

    public function isCompleted(): bool
    {
        return $this->invalid !== true && $this->complete === 1;
    }

    public function isFailed(): bool
    {
        return $this->invalid === true || $this->complete === 2;
    }
}
