<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Config\FlickrConfig;
use JOOservices\Flickr\Exceptions\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class FlickrConfigTest extends TestCase
{
    public function testAcceptsMinimalConfiguration(): void
    {
        $config = new FlickrConfig(apiKey: 'key', apiSecret: 'secret');

        self::assertSame('key', $config->apiKey);
        self::assertNull($config->callbackUrl);
        self::assertSame(FlickrConfig::DEFAULT_USER_AGENT, $config->userAgent);
        self::assertSame(600, $config->cacheTtl);
    }

    /**
     * @return iterable<string, array{0: string, 1: string, 2?: int|null, 3?: string|null, 4?: string|null}>
     */
    public static function provideInvalidConfigs(): iterable
    {
        yield 'blank key' => ['', 's'];
        yield 'blank secret' => ['k', '   '];
        yield 'negative ttl' => ['k', 's', -1];
        yield 'crlf user agent' => ['k', 's', null, "agent\r\nX-Evil: 1"];
        yield 'crlf callback' => ['k', 's', null, null, "https://ok.test\r\n"];
        yield 'non url callback' => ['k', 's', null, null, 'not-a-url'];
        yield 'http callback' => ['k', 's', null, null, 'http://app.test/oauth/callback'];
        yield 'callback with embedded userinfo' => ['k', 's', null, null, 'https://user:pass@app.test/oauth/callback'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideInvalidConfigs')]
    public function testRejectsInvalidConfiguration(
        string $apiKey,
        string $apiSecret,
        ?int $cacheTtl = null,
        ?string $userAgent = null,
        ?string $callbackUrl = null,
    ): void {
        $this->expectException(ConfigurationException::class);
        new FlickrConfig(
            apiKey: $apiKey,
            apiSecret: $apiSecret,
            callbackUrl: $callbackUrl,
            userAgent: $userAgent ?? FlickrConfig::DEFAULT_USER_AGENT,
            cacheTtl: $cacheTtl ?? 600,
        );
    }

    public function testZeroTtlIsAllowedButDisablesCachingDownstream(): void
    {
        $config = new FlickrConfig('k', 's', cacheTtl: 0);

        self::assertSame(0, $config->cacheTtl);
    }
}
