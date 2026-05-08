<?php

declare(strict_types=1);

namespace JOOservices\Flickr\DTO\Auth;

use JOOservices\Dto\Core\Dto;

final class RequestTokenData extends Dto
{
    public function __construct(
        public string $oauthToken,
        public string $oauthTokenSecret,
        public bool $oauthCallbackConfirmed = false,
    ) {}
}
