<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Cache\CacheKeyResolver;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;
use JOOservices\Flickr\Support\ParameterNormalizer;
use JOOservices\Flickr\Support\QueryString;
use JOOservices\Flickr\Support\SensitiveDataRedactor;
use PHPUnit\Framework\TestCase;

final class SupportTest extends TestCase
{
    /**
     * @return iterable<string, array{0: array<string, mixed>, 1: array<string, string|list<string>>}>
     */
    public static function provideNormalizerInput(): iterable
    {
        yield 'scalars' => [['a' => 1, 'b' => true, 'c' => false, 'd' => 1.5], ['a' => '1', 'b' => '1', 'c' => '0', 'd' => '1.5']];
        yield 'null dropped' => [['a' => null], []];
        yield 'list preserved' => [['id' => [1, 2]], ['id' => ['1', '2']]];
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, string|list<string>> $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideNormalizerInput')]
    public function testParameterNormalization(array $input, array $expected): void
    {
        self::assertSame($expected, ParameterNormalizer::normalize($input));
    }

    public function testNestedArraysAreRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ParameterNormalizer::normalize(['a' => ['b' => 'c']]);
    }

    /**
     * @return iterable<string, array{0: array<string, string|list<string>>, 1: string}>
     */
    public static function provideQueryRoundTrips(): iterable
    {
        yield 'dotted keys' => [['a.b' => 'c d'], 'a.b=c%20d'];
        yield 'plus stays encoded' => [['q' => 'a+b'], 'q=a%2Bb'];
        yield 'utf8' => [['t' => 'xin chào'], 't=xin%20ch%C3%A0o'];
        yield 'repeated values' => [['id' => ['1', '2']], 'id=1&id=2'];
    }

    /**
     * @param array<string, string|list<string>> $parameters
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideQueryRoundTrips')]
    public function testQueryStringBuildsRfc3986WithoutKeyRewriting(array $parameters, string $expected): void
    {
        self::assertSame($expected, QueryString::build($parameters));
    }

    public function testFormParsingPreservesDuplicateValuesAndDots(): void
    {
        self::assertSame(
            ['a.b' => 'c d', 'id' => ['1', '2']],
            QueryString::parseForm('a.b=c+d&id=1&id=2'),
        );
    }

    public function testCacheKeysAreStableAcrossParamOrderAndDistinctAcrossLists(): void
    {
        $one = CacheKeyResolver::key('flickr.photos.search', ['text' => 'x', 'page' => '1']);
        $two = CacheKeyResolver::key('flickr.photos.search', ['page' => '1', 'text' => 'x']);

        self::assertSame($one, $two);

        $listA = CacheKeyResolver::key('m', ['ids' => ['1', '2']]);
        $listB = CacheKeyResolver::key('m', ['ids' => ['2', '1']]);

        self::assertNotSame($listA, $listB);
        self::assertNotSame(CacheKeyResolver::key('a', []), CacheKeyResolver::key('b', []));
    }

    public function testRedactorMasksSecretKeysAndRegisteredValues(): void
    {
        $redactor = new SensitiveDataRedactor();
        $redactor->registerSecret('super-secret-value');

        $masked = $redactor->redactArray([
            'api_key' => 'visible-key',
            'oauth_token_secret' => 'leak',
            'note' => 'contains super-secret-value inside',
            'safe' => 'plain',
        ]);

        self::assertSame(SensitiveDataRedactor::MASK, $masked['api_key']);
        self::assertSame(SensitiveDataRedactor::MASK, $masked['oauth_token_secret']);
        self::assertSame('contains [redacted] inside', $masked['note']);
        self::assertSame('plain', $masked['safe']);

        self::assertSame('has [redacted] here', $redactor->redactText('has super-secret-value here'));
    }

    public function testRedactorCapsTrackedSecretsSoALongLivedInstanceCannotGrowUnbounded(): void
    {
        $redactor = new SensitiveDataRedactor();

        for ($i = 0; $i < 600; ++$i) {
            $redactor->registerSecret(sprintf('secret-value-%d', $i));
        }

        // The oldest registrations were evicted once the cap was exceeded...
        self::assertSame(
            'oldest secret-value-0 stays visible',
            $redactor->redactText('oldest secret-value-0 stays visible'),
        );
        // ...while recently-registered secrets are still redacted.
        self::assertSame(
            'newest [redacted] is masked',
            $redactor->redactText('newest secret-value-599 is masked'),
        );
    }

    public function testEnvelopeHelpersNavigateNestedShapes(): void
    {
        $response = new ApiResponseData(true, [
            'photos' => [
                'page' => 1,
                'photo' => [['id' => '1'], 'garbage-scalar'],
            ],
        ]);

        self::assertSame([['id' => '1']], $response->listAt('photos', 'photo'));
        self::assertSame(1, $response->mapAt('photos')['page']);
        self::assertSame([], $response->listAt('missing', 'path'));
    }

    public function testListAtWrapsASingleAssociativeObject(): void
    {
        $response = new ApiResponseData(true, [
            'uploader' => [
                'ticket' => ['id' => 'a', 'complete' => 1, 'photoid' => 'p-a'],
            ],
        ]);

        self::assertSame(
            [['id' => 'a', 'complete' => 1, 'photoid' => 'p-a']],
            $response->listAt('uploader', 'ticket'),
        );
        self::assertSame([], $response->listAt('uploader', 'missing'));
    }
}
