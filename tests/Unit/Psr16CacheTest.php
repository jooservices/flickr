<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Cache\Psr16Cache;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Exceptions\CacheBackendException;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache as SymfonyPsr16;

final class Psr16CacheTest extends TestCase
{
    public function testMissReturnsNull(): void
    {
        $cache = new Psr16Cache(new SymfonyPsr16(new ArrayAdapter()));

        self::assertNull($cache->get('missing'));
    }

    public function testPutThenGetRoundTripsTheResponse(): void
    {
        $cache = new Psr16Cache(new SymfonyPsr16(new ArrayAdapter()));
        $response = new ApiResponseData(true, ['a' => 1]);

        $cache->put('key', $response, 60);
        $hit = $cache->get('key');

        self::assertNotNull($hit);
        self::assertTrue($hit->ok);
        self::assertSame(['a' => 1], $hit->data);
    }

    public function testNonPositiveTtlNeverWritesToTheBackend(): void
    {
        $backend = new class implements CacheInterface {
            public bool $setCalled = false;

            public function get(string $key, mixed $default = null): mixed
            {
                return $default;
            }

            public function set(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
            {
                $this->setCalled = true;

                return true;
            }

            public function delete(string $key): bool
            {
                return true;
            }

            public function clear(): bool
            {
                return true;
            }

            public function getMultiple(iterable $keys, mixed $default = null): iterable
            {
                return [];
            }

            /**
             * @param iterable<mixed> $values
             */
            public function setMultiple(iterable $values, int|\DateInterval|null $ttl = null): bool
            {
                return true;
            }

            public function deleteMultiple(iterable $keys): bool
            {
                return true;
            }

            public function has(string $key): bool
            {
                return false;
            }
        };

        (new Psr16Cache($backend))->put('key', new ApiResponseData(true), 0);

        self::assertFalse($backend->setCalled);
    }

    public function testBackendFailureOnGetIsWrappedInACacheBackendException(): void
    {
        $backend = new class implements CacheInterface {
            public function get(string $key, mixed $default = null): mixed
            {
                throw new RuntimeException('backend down');
            }

            public function set(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
            {
                return true;
            }

            public function delete(string $key): bool
            {
                return true;
            }

            public function clear(): bool
            {
                return true;
            }

            public function getMultiple(iterable $keys, mixed $default = null): iterable
            {
                return [];
            }

            /**
             * @param iterable<mixed> $values
             */
            public function setMultiple(iterable $values, int|\DateInterval|null $ttl = null): bool
            {
                return true;
            }

            public function deleteMultiple(iterable $keys): bool
            {
                return true;
            }

            public function has(string $key): bool
            {
                return false;
            }
        };

        $this->expectException(CacheBackendException::class);
        (new Psr16Cache($backend))->get('key');
    }

    public function testBackendFailureOnPutIsWrappedInACacheBackendException(): void
    {
        $backend = new class implements CacheInterface {
            public function get(string $key, mixed $default = null): mixed
            {
                return $default;
            }

            public function set(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
            {
                throw new RuntimeException('backend down');
            }

            public function delete(string $key): bool
            {
                return true;
            }

            public function clear(): bool
            {
                return true;
            }

            public function getMultiple(iterable $keys, mixed $default = null): iterable
            {
                return [];
            }

            /**
             * @param iterable<mixed> $values
             */
            public function setMultiple(iterable $values, int|\DateInterval|null $ttl = null): bool
            {
                return true;
            }

            public function deleteMultiple(iterable $keys): bool
            {
                return true;
            }

            public function has(string $key): bool
            {
                return false;
            }
        };

        $this->expectException(CacheBackendException::class);
        (new Psr16Cache($backend))->put('key', new ApiResponseData(true), 60);
    }
}
