<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Contracts;

use Psr\Http\Message\RequestInterface;
use JOOservices\Client\Dto\RequestOptions;
use JOOservices\Flickr\Dtos\Common\RawResponseData;

interface FlickrTransport
{
    public function send(RequestInterface $request, RequestOptions $options): RawResponseData;
}
