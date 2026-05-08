<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Auth;

interface FlickrSignerContract
{
    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, string>
     */
    public function sign(
        string $method,
        string $url,
        array $parameters = [],
        ?string $token = null,
        ?string $tokenSecret = null,
        ?string $nonce = null,
        ?int $timestamp = null,
    ): array;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function signatureBaseString(string $method, string $url, array $parameters): string;
}
