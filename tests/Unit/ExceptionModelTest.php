<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Exceptions\Contracts\JOOExceptionInterface;
use JOOservices\Flickr\Exceptions\ApiException;
use JOOservices\Flickr\Exceptions\FlickrErrorCodeMap;
use JOOservices\Flickr\Exceptions\FlickrException;
use JOOservices\Flickr\Tests\TestCase;

final class ExceptionModelTest extends TestCase
{
    public function test_flickr_exception_is_joo_runtime_exception(): void
    {
        $exception = new ApiException('boom', 1, 0, null, 503, true);

        $this->assertInstanceOf(FlickrException::class, $exception);
        $this->assertInstanceOf(JOOExceptionInterface::class, $exception);
        $this->assertSame(1, $exception->apiCode());
        $this->assertSame(503, $exception->httpStatus());
        $this->assertTrue($exception->retryable());
    }

    public function test_error_code_map(): void
    {
        $this->assertTrue(FlickrErrorCodeMap::isAuthCode(98));
        $this->assertFalse(FlickrErrorCodeMap::isAuthCode(1));
        $this->assertTrue(FlickrErrorCodeMap::isRetryable(105));
        $this->assertFalse(FlickrErrorCodeMap::isRetryable(1));
    }
}
