<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Integration;

use JOOservices\Client\Client\ClientBuilder;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Dtos\Photos\SearchPhotosData;
use JOOservices\Flickr\Exceptions\UnavailableMethodException;
use JOOservices\Flickr\FlickrFactory;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use JOOservices\Flickr\Services\AbstractApiService;
use JOOservices\Flickr\Services\PhotosApi;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use JOOservices\Flickr\Upload\UploadOptions;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Full-stack consumer flow over client v4 fakes: every generated wrapper is
 * dispatched against the real pipeline exactly once, plus typed/OAuth/upload
 * smoke paths — all offline.
 */
final class FullStackFakeTest extends TestCase
{
    private \JOOservices\Flickr\Testing\FlickrFake $fake;

    private \JOOservices\Flickr\Flickr $flickr;

    private InMemoryTokenStore $tokens;

    protected function setUp(): void
    {
        $this->fake = \JOOservices\Flickr\Testing\FlickrFake::create();
        $this->tokens = new InMemoryTokenStore();
        $this->tokens->put(new \JOOservices\Flickr\Auth\AccessTokenData(
            'parity-token',
            'parity-secret',
            \JOOservices\Flickr\Enums\AuthPermission::Delete,
        ));
        $client = ClientBuilder::create()->build();
        $this->flickr = FlickrFactory::make(
            config: PipelineFactory::config(),
            http: $client,
            tokens: $this->tokens,
        );
    }

    protected function tearDown(): void
    {
        $this->fake->close();
    }

    public function testEveryGeneratedWrapperDispatchesItsExactRegistryMethod(): void
    {
        $registry = new FlickrMethodRegistry();
        $facade = new ReflectionClass($this->flickr);
        $position = 0;

        foreach ($facade->getMethods(ReflectionMethod::IS_PUBLIC) as $accessor) {
            if ($accessor->getDeclaringClass()->getName() !== $facade->getName()) {
                continue;
            }

            if (in_array($accessor->getName(), ['__construct', 'api', 'oauth', 'uploads'], true)) {
                continue;
            }

            $service = $accessor->invoke($this->flickr);
            self::assertInstanceOf(AbstractApiService::class, $service);

            $serviceReflection = new ReflectionClass($service);
            $skipped = self::TYPED_SKIPPED[$serviceReflection->getName()] ?? [];

            foreach ($serviceReflection->getMethods(ReflectionMethod::IS_PUBLIC) as $wrapper) {
                if ($wrapper->getDeclaringClass()->getName() !== $serviceReflection->getName()) {
                    continue;
                }

                if (in_array($wrapper->getName(), $skipped, true)) {
                    continue;
                }

                $expectedMethod = $this->expectedRegistryMethod($accessor->getName(), $wrapper->getName());
                $definition = (new FlickrMethodRegistry())->find($expectedMethod);

                if ($definition === null) {
                    self::fail("Wrapper {$accessor->getName()}::{$wrapper->getName()} has no registry record.");
                }

                if ($definition->available === false) {
                    $beforeUnavailable = count($this->fake->recorded());

                    try {
                        $wrapper->invoke($service, [], null);
                        self::fail("{$expectedMethod} must fail as unavailable.");
                    } catch (UnavailableMethodException) {
                        self::assertCount($beforeUnavailable, $this->fake->recorded());
                    }

                    continue;
                }

                $invocationCountBefore = count($this->fake->recorded());
                $this->fake->queueJson(['stat' => 'ok']);

                try {
                    $result = $wrapper->invoke($service, [], null);
                } catch (\Throwable $error) {
                    if ($error instanceof UnavailableMethodException) {
                        continue;
                    }

                    throw $error;
                }

                self::assertInstanceOf(ApiResponseData::class, $result);
                self::assertCount($invocationCountBefore + 1, $this->fake->recorded(), sprintf(
                    '%s::%s did not dispatch exactly one request.',
                    $service::class,
                    $wrapper->getName(),
                ));

                $dispatched = PipelineFactory::dispatchedParameters(
                    $this->fake->recorded()[$invocationCountBefore]->request,
                );

                $dispatchedMethod = $dispatched['method'] ?? null;
                self::assertSame($expectedMethod, $dispatchedMethod, sprintf(
                    '%s::%s dispatched %s instead of %s',
                    $service::class,
                    $wrapper->getName(),
                    is_string($dispatchedMethod) ? $dispatchedMethod : '(none)',
                    $expectedMethod,
                ));
            }
        }
    }

    private const TYPED_SKIPPED = [
        PhotosApi::class => ['search', 'getRecent', 'getInfo', 'getSizes', 'getExif'],
    ];

    /** @return list<string> */
    private function surfaceMethodsFor(string $accessor): array
    {
        /** @var array<string, mixed> $surface */
        $surface = require __DIR__ . '/../../resources/api-surface.php';
        $services = self::mapOf($surface['services'] ?? null);

        if (isset($services[$accessor]) === false) {
            self::fail("No api-surface entry for {$accessor}");
        }

        $spec = self::mapOf($services[$accessor]);
        $registry = new FlickrMethodRegistry();
        $methods = [];

        foreach ($registry->names() as $name) {
            $matched = false;

            foreach (self::listOfStrings($spec['prefixes'] ?? []) as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    $matched = true;
                    break;
                }
            }

            foreach (self::listOfStrings($spec['exclude_prefixes'] ?? []) as $excluded) {
                if (str_starts_with($name, $excluded)) {
                    $matched = false;
                    break;
                }
            }

            if ($matched && in_array($name, self::listOfStrings($spec['exclude_methods'] ?? []), true)) {
                $matched = false;
            }

            if ($matched) {
                $methods[] = $name;
            }
        }

        foreach (self::listOfStrings($spec['methods'] ?? []) as $explicit) {
            if (in_array($explicit, $methods, true) === false) {
                $methods[] = $explicit;
            }
        }

        return $methods;
    }

    /**
     * @param mixed $value
     *
     * @return array<string, mixed>
     */
    private static function mapOf(mixed $value): array
    {
        $map = [];

        foreach ((array) $value as $key => $item) {
            $map[(string) $key] = $item;
        }

        return $map;
    }

    /**
     * @param mixed $value
     *
     * @return list<string>
     */
    private static function listOfStrings(mixed $value): array
    {
        $items = [];

        foreach ((array) $value as $item) {
            if (is_string($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param list<string> $names
     *
     * @return list<string>
     */
    private static function shallowest(array $names): array
    {
        if ($names === []) {
            return $names;
        }

        $depths = array_map(static fn(string $n): int => substr_count($n, '.'), $names);
        $min = min($depths);
        $picked = array_values(array_filter($names, static fn(string $n): bool => substr_count($n, '.') === $min));

        return count($picked) === 1 ? $picked : $names;
    }

    private function expectedRegistryMethod(string $accessor, string $wrapper): string
    {
        $byTail = [];
        $byAltTail = [];

        foreach ($this->surfaceMethodsFor($accessor) as $name) {
            $tail = substr($name, (int) strrpos($name, '.') + 1);
            $head = substr($name, 0, (int) strrpos($name, '.'));
            $altTail = substr((string) strrchr($head, '.'), 1) . ucfirst($tail);

            if ($tail === $wrapper) {
                $byTail[] = $name;
            }

            if ($altTail === $wrapper) {
                $byAltTail[] = $name;
            }
        }

        $candidates = match (true) {
            count($byTail) === 1 => $byTail,
            count($byAltTail) === 1 => $byAltTail,
            count($byTail) > 1 => self::shallowest($byTail),
            default => [],
        };

        self::assertNotNull($candidates[0] ?? null, "No registry method matches {$accessor}->{$wrapper}()");
        self::assertCount(
            1,
            array_unique($candidates),
            sprintf('[%s->%s] matched multiple registry methods: %s', $accessor, $wrapper, implode(', ', $candidates)),
        );

        return $candidates[0];
    }

    public function testTypedSearchFlowsThroughFullStack(): void
    {
        $this->fake->queueJson([
            'stat' => 'ok',
            'photos' => ['page' => 1, 'pages' => 4, 'total' => '400', 'photo' => [
                ['id' => 'a1', 'owner' => 'me', 'title' => 'T', 'secret' => 'sec', 'server' => 'sv', 'farm' => 6],
            ]],
        ]);

        $result = $this->flickr->photos()->search(new SearchPhotosData(text: 'hanoi', perPage: 30));

        self::assertSame('a1', $result->photos[0]->id);
        self::assertSame(400, $result->total);

        $this->fake->assertCall(0, 'flickr.photos.search', ['text' => 'hanoi', 'per_page' => '30']);
    }

    public function testRawFallbackRequiresExplicitModeAndNeverCaches(): void
    {
        $this->fake->queueJson(['stat' => 'ok']);
        $options = new \JOOservices\Flickr\Api\RawCallOptions(\JOOservices\Flickr\Enums\AuthenticationMode::Unauthenticated);

        $response = $this->flickr->api()->raw('flickr.future.method', \JOOservices\Flickr\Enums\HttpMethod::Get, $options, ['q' => 'x']);

        self::assertTrue($response->ok);
        $this->fake->assertCall(0, 'flickr.future.method', ['q' => 'x']);
    }

    public function testOAuthTransactionCompletesThroughFullStack(): void
    {
        $this->fake->queueRaw(new RawResponseData(200, [], 'oauth_callback_confirmed=true&oauth_token=req&oauth_token_secret=reqs'));
        $this->fake->queueRaw(new RawResponseData(200, [], 'oauth_token=acc&oauth_token_secret=accs'));

        $pending = $this->flickr->oauth()->begin(\JOOservices\Flickr\Enums\AuthPermission::Write);
        $token = $this->flickr->oauth()->complete($pending, new \JOOservices\Flickr\Auth\OAuthCallback('req', 'v'));

        self::assertSame('acc', $token->token);
        self::assertSame(\JOOservices\Flickr\Enums\AuthPermission::Write, $this->tokens->get()?->permission);
    }

    public function testUploadFlowsThroughFullStackWithMultipartBody(): void
    {
        $this->tokens->put(new \JOOservices\Flickr\Auth\AccessTokenData('t', 's', \JOOservices\Flickr\Enums\AuthPermission::Write));
        $path = tempnam(sys_get_temp_dir(), 'up');

        if ($path === false) {
            self::fail('Unable to create the temporary upload fixture.');
        }

        file_put_contents($path, 'bytes');

        $this->fake->queueXml('<rsp stat="ok"><photo id="9"/></rsp>');

        $result = $this->flickr->uploads()->upload($path, new UploadOptions(title: 'x'));
        unlink($path);

        self::assertSame('9', $result->photoId);
        self::assertStringContainsString('multipart/form-data; boundary=', $this->fake->recorded()[0]->request->getHeaderLine('Content-Type'));
    }

    public function testLegacyAuthAccessorFailsBeforeNetwork(): void
    {
        $count = count($this->fake->recorded());

        $this->expectException(UnavailableMethodException::class);
        $this->flickr->legacyAuth()->getToken();

        self::assertCount($count, $this->fake->recorded());
    }

    public function testPandaAccessorFailsBeforeNetwork(): void
    {
        $this->expectException(UnavailableMethodException::class);
        $this->flickr->panda()->getList();
    }

    public function testFacadeHasNoMagicDispatch(): void
    {
        $reflection = new ReflectionClass($this->flickr);
        self::assertFalse($reflection->hasMethod('__call'));
        self::assertFalse($reflection->hasMethod('__callStatic'));
    }

    public function testDescribeExposesLocalMetadata(): void
    {
        $info = $this->flickr->api()->describe('flickr.photos.search');

        self::assertNotNull($info);
        self::assertTrue($info->cacheable);
        self::assertNull($this->flickr->api()->describe('flickr.not.real'));
    }
}
