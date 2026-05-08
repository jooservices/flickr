<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts\Client;

use JOOservices\Flickr\DTO\Common\RawResponseData;

interface FlickrTransportContract
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function request(string $method, string $url, array $options = []): RawResponseData;
}
