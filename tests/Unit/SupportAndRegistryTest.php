<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Enums\Privacy;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Support\ParameterNormalizer;
use JOOservices\Flickr\Tests\TestCase;

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
}
