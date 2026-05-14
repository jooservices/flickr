<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Metadata\FlickrMethodDefinition;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Tests\Fakes\FakeTransport;
use JOOservices\Flickr\Tests\TestCase;

final class OfficialMethodCoverageTest extends TestCase
{
    public function test_registry_contains_every_official_flickr_api_method(): void
    {
        $official = $this->officialMethods();
        $registered = require __DIR__.'/../../src/Metadata/methods.php';

        $this->assertCount(224, $official);

        foreach ($official as $method) {
            $this->assertArrayHasKey($method, $registered);
            $this->assertInstanceOf(FlickrMethodDefinition::class, $registered[$method]);
            $this->assertSame($method, $registered[$method]->name);
            $this->assertSame('https://www.flickr.com/services/api/'.$method.'.html', $registered[$method]->docsUrl);
            $this->assertNotSame('', $registered[$method]->httpMethod->value);
            $this->assertIsBool($registered[$method]->cacheable);
        }
    }

    public function test_registry_verification_tool_passes_against_local_fixture(): void
    {
        exec('php '.escapeshellarg(__DIR__.'/../../tools/verify-method-registry.php'), $output, $exitCode);

        $this->assertSame(0, $exitCode, implode(PHP_EOL, $output));
        $this->assertSame('Verified 224 official Flickr REST method definitions.', $output[0] ?? '');
    }

    public function test_sensitive_auth_upload_and_mutation_methods_are_not_cacheable(): void
    {
        $registered = require __DIR__.'/../../src/Metadata/methods.php';

        foreach ($registered as $method => $definition) {
            if (str_starts_with($method, 'flickr.auth.')) {
                $this->assertFalse($definition->cacheable, "{$method} must not be cacheable.");
            }

            if ($method === 'flickr.photos.upload.checkTickets') {
                $this->assertFalse($definition->cacheable, "{$method} must not be cacheable.");
            }

            if ($definition->httpMethod->value === 'POST') {
                $this->assertFalse($definition->cacheable, "{$method} mutations must not be cacheable.");
            }

            if ($definition->authPermission !== null) {
                $this->assertFalse($definition->cacheable, "{$method} permissioned calls must not be cacheable.");
            }

            if ($definition->requiresAuth) {
                $this->assertFalse($definition->cacheable, "{$method} authenticated calls must not be cacheable by default.");
            }
        }

        $unknown = FlickrMethodRegistry::default()->find('flickr.future.method');
        $this->assertFalse($unknown->cacheable);
    }

    public function test_root_services_expose_wrapper_methods_for_every_official_method(): void
    {
        $flickr = FlickrFactory::make(
            new FlickrConfig('key', 'secret'),
            transport: new FakeTransport,
        );

        foreach ($this->officialMethods() as $method) {
            $accessor = $this->accessor($this->categoryOf($method));
            $wrapper = $this->wrapperMethod($method);

            $this->assertTrue(method_exists($flickr, $accessor), "{$accessor} accessor is missing.");
            $service = $flickr->{$accessor}();
            $this->assertTrue(method_exists($service, $wrapper), "{$accessor}()->{$wrapper}() is missing for {$method}.");
        }
    }

    public function test_all_apis_example_catalog_covers_every_official_method(): void
    {
        require_once __DIR__.'/../../examples/all-apis.php';

        $this->assertSame($this->officialMethods(), array_keys(flickr_all_api_definitions()));
    }

    public function test_generated_wrappers_call_expected_raw_method_and_apply_registry_auth_metadata(): void
    {
        $transport = new FakeTransport;
        $transport->push('{"stat":"ok"}');
        $transport->push('{"stat":"ok"}');

        $flickr = FlickrFactory::make(new FlickrConfig('key', 'secret'), transport: $transport);
        $flickr->tokens()->put(new AccessTokenData('token', 'token-secret'));

        $flickr->tags()->getHotList(['count' => 5]);
        $this->assertSame('flickr.tags.getHotList', $transport->requests[0]['options']['query']['method']);
        $this->assertArrayNotHasKey('oauth_token', $transport->requests[0]['options']['query']);

        $flickr->favorites()->add(['photo_id' => '123']);
        $this->assertSame('POST', $transport->requests[1]['method']);
        $this->assertSame('flickr.favorites.add', $transport->requests[1]['options']['form_params']['method']);
        $this->assertSame('token', $transport->requests[1]['options']['form_params']['oauth_token']);
    }

    /**
     * @return list<string>
     */
    private function officialMethods(): array
    {
        return require __DIR__.'/../Fixtures/official-flickr-methods.php';
    }

    private function categoryOf(string $method): string
    {
        $parts = explode('.', $method);
        $category = $parts[1];
        $nested = [
            'auth' => ['oauth'],
            'groups' => ['members', 'pools'],
            'photos' => ['comments', 'geo', 'licenses', 'notes', 'people', 'suggestions', 'transform', 'upload'],
            'photosets' => ['comments'],
        ];

        if (in_array($parts[2] ?? '', $nested[$category] ?? [], true)) {
            return $category.'.'.$parts[2];
        }

        if ($category === 'groups' && ($parts[2] ?? null) === 'discuss') {
            return 'groups.discuss.'.($parts[3] ?? 'topics');
        }

        return $category;
    }

    private function accessor(string $category): string
    {
        return match ($category) {
            'auth' => 'authApi',
            'auth.oauth' => 'authOauthApi',
            default => lcfirst(str_replace(' ', '', ucwords(str_replace('.', ' ', $category)))),
        };
    }

    private function wrapperMethod(string $method): string
    {
        $parts = explode('.', $method);

        return end($parts);
    }
}
