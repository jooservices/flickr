<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Dtos\Common\RawResponseData;
use JOOservices\Flickr\Pagination\PaginationOptions;
use JOOservices\Flickr\Pagination\Paginator;
use JOOservices\Flickr\Tests\Support\FakeTransport;
use JOOservices\Flickr\Tests\Support\PipelineFactory;
use PHPUnit\Framework\TestCase;

final class PaginationTest extends TestCase
{
    private function searchPage(int $page, int $pages): string
    {
        return (string) json_encode([
            'stat' => 'ok',
            'photos' => ['page' => $page, 'pages' => $pages, 'total' => (string) ($pages * 10), 'photo' => [['id' => (string) $page]]],
        ]);
    }

    public function testPaginatorIsLazyBoundedAndStopsAtReportedTotal(): void
    {
        $transport = new FakeTransport();

        foreach ([1, 2, 3] as $page) {
            $transport->queue(new RawResponseData(200, [], $this->searchPage($page, 3)));
        }

        $paginator = new Paginator(PipelineFactory::api($transport), 'flickr.photos.search');
        $seen = [];

        foreach ($paginator->pages(new PaginationOptions(maxPages: 99)) as $page => $response) {
            $seen[] = $page;
            self::assertTrue($response->ok);
        }

        self::assertSame([1, 2, 3], $seen);
        self::assertSame(3, $transport->sentCount());
    }

    public function testMaxPagesCapsIterationEvenWhenMorePagesExist(): void
    {
        $transport = new FakeTransport();
        $transport->queue(new RawResponseData(200, [], $this->searchPage(1, 50)));

        $paginator = new Paginator(PipelineFactory::api($transport), 'flickr.photos.search');

        iterator_to_array($paginator->pages(new PaginationOptions(maxPages: 1)));

        self::assertSame(1, $transport->sentCount());
    }

    public function testEmptyPageStopsWhenConfigured(): void
    {
        $transport = new FakeTransport();
        $transport->queue(
            new RawResponseData(200, [], $this->searchPage(1, 5)),
            new RawResponseData(200, [], (string) json_encode(['stat' => 'ok', 'photos' => ['page' => 2, 'pages' => 5, 'photo' => []]])),
        );

        $paginator = new Paginator(PipelineFactory::api($transport), 'flickr.photos.search');
        $pages = [];

        foreach ($paginator->pages(new PaginationOptions(maxPages: 5)) as $page => $response) {
            $pages[] = $page;
        }

        self::assertSame([1, 2], $pages);
        self::assertSame(2, $transport->sentCount());
    }

    public function testPaginationOptionsRejectUnboundedConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginationOptions(maxPages: 0);
    }

    public function testPaginationOptionsRejectAnAbsurdlyHighPageCeiling(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PaginationOptions(maxPages: PHP_INT_MAX);
    }
}
