<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Enums\AuthPermission;
use JOOservices\Flickr\Enums\HttpMethod;
use JOOservices\Flickr\Metadata\FlickrMethodRegistry;
use PHPUnit\Framework\TestCase;

final class RegistryTest extends TestCase
{
    private FlickrMethodRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new FlickrMethodRegistry();
    }

    public function testRegistryHoldsExactly224Methods(): void
    {
        self::assertCount(224, $this->registry->all());
        self::assertSame($this->registry->names(), array_values(array_unique($this->registry->names())));
    }

    public function testNamespaceCountsMatchFrozenBaseline(): void
    {
        $counts = [];

        foreach ($this->registry->names() as $name) {
            $namespace = explode('.', $name)[1];
            $counts[$namespace] = ($counts[$namespace] ?? 0) + 1;
        }

        self::assertSame(60, $counts['photos']);
        self::assertSame(19, $counts['groups']);
        self::assertSame(18, $counts['photosets']);
        self::assertSame(16, $counts['stats']);
        self::assertSame(15, $counts['places']);
        self::assertSame(6, $counts['auth']);
        self::assertSame(2, $counts['panda']);
    }

    public function testLegacyAndPandaMethodsAreUnavailableMetadata(): void
    {
        foreach (['flickr.auth.getToken', 'flickr.auth.oauth.getAccessToken', 'flickr.panda.getList'] as $name) {
            $definition = $this->registry->find($name);
            self::assertNotNull($definition);
            self::assertFalse($definition->available);
            self::assertNotNull($definition->deprecationReason);
        }
    }

    public function testTicketCheckingIsNeverCacheable(): void
    {
        $checkTickets = $this->registry->find('flickr.photos.upload.checkTickets');
        self::assertNotNull($checkTickets);
        self::assertFalse($checkTickets->cacheable);
    }

    public function testVerbAndAuthMetadataAreTyped(): void
    {
        $delete = $this->registry->find('flickr.photos.delete');
        self::assertNotNull($delete);
        self::assertSame(HttpMethod::Post, $delete->httpMethod);
        self::assertTrue($delete->requiresAuth());

        $search = $this->registry->find('flickr.photos.search');
        self::assertNotNull($search);
        self::assertSame(HttpMethod::Get, $search->httpMethod);
        self::assertSame(AuthPermission::None, $search->permission);

        $uploadStatus = $this->registry->find('flickr.people.getUploadStatus');
        self::assertNotNull($uploadStatus);
        self::assertTrue($uploadStatus->requiresAuth());
    }

    public function testFindReturnsNullForUnknownNames(): void
    {
        self::assertNull($this->registry->find('flickr.not.real'));
    }

    public function testSuggestFindsACloseTypo(): void
    {
        self::assertSame('flickr.photos.search', $this->registry->suggest('flickr.photos.serch'));
    }

    public function testSuggestReturnsNullForUnrelatedGarbage(): void
    {
        self::assertNull($this->registry->suggest('totally-unrelated-garbage-input'));
    }
}
