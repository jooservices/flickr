<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\AccessTokenData;
use JOOservices\Flickr\Auth\NullTokenStore;
use JOOservices\Flickr\Enums\AuthPermission;
use PHPUnit\Framework\TestCase;

final class NullTokenStoreTest extends TestCase
{
    public function testGetIsAlwaysNull(): void
    {
        self::assertNull((new NullTokenStore())->get());
    }

    public function testPutIsDiscardedSilently(): void
    {
        $store = new NullTokenStore();
        $store->put(new AccessTokenData('tok', 'sec', AuthPermission::Write));

        self::assertNull($store->get());
    }

    public function testForgetIsANoOp(): void
    {
        $store = new NullTokenStore();
        $store->forget();

        self::assertNull($store->get());
    }
}
