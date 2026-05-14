<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Fakes;

use JOOservices\Flickr\Contracts\Auth\FlickrSignerContract;

final class SpySigner implements FlickrSignerContract
{
    public int $signCalls = 0;

    public function sign(
        string $method,
        string $url,
        array $parameters = [],
        ?string $token = null,
        ?string $tokenSecret = null,
        ?string $nonce = null,
        ?int $timestamp = null,
    ): array {
        $this->signCalls++;

        return [
            'oauth_token' => (string) $token,
            'oauth_signature' => 'signed',
        ];
    }

    public function signatureBaseString(string $method, string $url, array $parameters): string
    {
        return 'base';
    }
}
