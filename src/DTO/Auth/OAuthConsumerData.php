<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Auth;

use JOOservices\Dto\Core\Dto;

final class OAuthConsumerData extends Dto
{
    public function __construct(
        public string $key,
        public string $secret,
        public ?string $callbackUrl = null,
    ) {}
}
