<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use DateInterval;
use JOOservices\Flickr\Cache\NullCache;
use JOOservices\Flickr\Cache\Psr16Cache;
use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Exceptions\UploadException;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Support\FileValidator;
use JOOservices\Flickr\Support\ParameterNormalizer;
use JOOservices\Flickr\Support\QueryString;
use JOOservices\Flickr\Support\SignatureBaseStringBuilder;
use JOOservices\Flickr\Support\UrlBuilder;
use JOOservices\Flickr\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

final class SupportAndRegistryTest extends TestCase
{
    public function test_parameter_normalizer_removes_nulls_and_converts_flickr_values(): void
    {
        $normalized = (new ParameterNormalizer)->normalize([
            'null' => null,
            'tags' => ['php', 'sdk'],
            'enabled' => true,
            'privacy' => Privacy::Public,
        ]);

        $this->assertArrayNotHasKey('null', $normalized);
        $this->assertSame('php,sdk', $normalized['tags']);
        $this->assertSame(1, $normalized['enabled']);
        $this->assertSame('public', $normalized['privacy']);
    }

    public function test_method_registry_known_and_unknown_fallback(): void
    {
        $registry = FlickrMethodRegistry::default();
        $delete = $registry->find('flickr.photos.delete');
        $unknown = $registry->find('flickr.future.method');

        $this->assertTrue($delete->requiresAuth);
        $this->assertSame(AuthPermission::Delete, $delete->authPermission);
        $this->assertSame(HttpMethod::Post, $delete->httpMethod);
        $this->assertFalse($unknown->requiresAuth);
        $this->assertSame(HttpMethod::Get, $unknown->httpMethod);
    }

    public function test_query_strings_and_signature_base_strings_handle_repeated_values_and_ports(): void
    {
        $this->assertSame('flag=1&tags=php&tags=sdk', QueryString::build([
            'flag' => true,
            'tags' => ['php', 'sdk'],
        ]));

        $builder = new SignatureBaseStringBuilder;
        $base = $builder->build('GET', 'https://Example.test:8443/rest?ignored=1', [
            'b' => 'two',
            'a' => ['z', 'a'],
            'oauth_signature' => 'ignored',
            'empty' => null,
        ]);

        $this->assertStringStartsWith('GET&https%3A%2F%2Fexample.test%3A8443%2Frest&', $base);
        $this->assertStringContainsString('a%3Da%26a%3Dz%26b%3Dtwo', $base);

        $defaultPort = $builder->build('GET', 'https://Example.test:443/rest', ['a' => 'b']);
        $this->assertStringContainsString('https%3A%2F%2Fexample.test%2Frest', $defaultPort);
    }

    public function test_file_validator_accepts_readable_files_and_rejects_bad_paths(): void
    {
        $validator = new FileValidator;
        $file = tempnam(sys_get_temp_dir(), 'flickr-readable-');
        file_put_contents($file, 'bytes');

        try {
            $validator->validateReadableFile($file);
            $this->addToAssertionCount(1);
        } finally {
            @unlink($file);
        }

        foreach (['', sys_get_temp_dir(), tempnam(sys_get_temp_dir(), 'flickr-empty-')] as $path) {
            try {
                $validator->validateReadableFile($path);
                $this->fail("Expected invalid file path [{$path}].");
            } catch (UploadException) {
                $this->addToAssertionCount(1);
            } finally {
                if (is_file($path)) {
                    @unlink($path);
                }
            }
        }
    }

    public function test_url_builder_normalizes_paths(): void
    {
        $builder = new UrlBuilder;

        $this->assertSame('https://example.test/rest?method=flickr.test.echo', $builder->withQuery('https://example.test/rest', [
            'method' => 'flickr.test.echo',
        ]));
        $this->assertSame('https://example.test/rest?format=json&method=flickr.test.echo', $builder->withQuery('https://example.test/rest?format=json', [
            'method' => 'flickr.test.echo',
        ]));
        $this->assertSame('https://example.test/rest', $builder->withQuery('https://example.test/rest', []));
    }

    public function test_cache_adapters_delegate_and_discard_values(): void
    {
        $psr = new class implements CacheInterface
        {
            /**
             * @var array<string, mixed>
             */
            public array $items = [];

            public function get(string $key, mixed $default = null): mixed
            {
                return $this->items[$key] ?? $default;
            }

            public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
            {
                $this->items[$key] = [$value, $ttl];

                return true;
            }

            public function delete(string $key): bool
            {
                unset($this->items[$key]);

                return true;
            }

            public function clear(): bool
            {
                $this->items = [];

                return true;
            }

            public function getMultiple(iterable $keys, mixed $default = null): iterable
            {
                foreach ($keys as $key) {
                    yield $key => $this->get((string) $key, $default);
                }
            }

            public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
            {
                foreach ($values as $key => $value) {
                    $this->set((string) $key, $value, $ttl);
                }

                return true;
            }

            public function deleteMultiple(iterable $keys): bool
            {
                foreach ($keys as $key) {
                    $this->delete((string) $key);
                }

                return true;
            }

            public function has(string $key): bool
            {
                return array_key_exists($key, $this->items);
            }
        };
        $cache = new Psr16Cache($psr);

        $cache->put('key', 'value', 60);
        $this->assertSame(['value', 60], $cache->get('key'));
        $cache->forget('key');
        $this->assertNull($cache->get('key'));

        $null = new NullCache;
        $null->put('key', 'value');
        $this->assertNull($null->get('key'));
        $null->forget('key');
        $this->assertNull($null->get('key'));
    }
}
