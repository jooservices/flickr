<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Contracts\NonceGenerator;

final class RandomNonceGenerator implements NonceGenerator
{
    public function generate(): string
    {
        return bin2hex(random_bytes(16));
    }
}
