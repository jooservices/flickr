<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Upload;

use JOOservices\Dto\Core\Dto;
use JOOservices\Flickr\Enums\ContentType;
use JOOservices\Flickr\Enums\HiddenStatus;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Enums\SafetyLevel;

final class UploadPhotoData extends Dto
{
    /**
     * @param  list<string>  $tags
     */
    public function __construct(
        public string $path,
        public ?string $title = null,
        public ?string $description = null,
        public array $tags = [],
        public Privacy $privacy = Privacy::Private,
        public SafetyLevel $safetyLevel = SafetyLevel::Safe,
        public ContentType $contentType = ContentType::Photo,
        public HiddenStatus $hidden = HiddenStatus::Visible,
        public bool $async = false,
    ) {}
}
