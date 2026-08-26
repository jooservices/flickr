<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Upload;

final class UploadOptions
{
    /**
     * @param list<string> $tags
     */
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly array $tags = [],
        public readonly ?bool $isPublic = null,
        public readonly ?bool $isFriend = null,
        public readonly ?bool $isFamily = null,
        public readonly bool $async = false,
    ) {
    }
}
