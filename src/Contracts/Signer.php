<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

interface Signer
{
    public function sign(string $baseString, string $signingKey): string;
}
