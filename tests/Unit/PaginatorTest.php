<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;
use JOOservices\Flickr\Pagination\Paginator;
use JOOservices\Flickr\Tests\TestCase;

final class PaginatorTest extends TestCase
{
    public function test_pages_stops_on_default_empty_detection(): void
    {
        $paginator = new Paginator;
        $calls = 0;

        $pages = iterator_to_array($paginator->pages(
            function (int $page, ?int $perPage) use (&$calls): ApiResponseData {
                $calls++;

                return new ApiResponseData(
                    ok: true,
                    data: ['items' => []],
                    pagination: new PaginationData($page, 3, $perPage ?? 10, 0),
                );
            },
            new PaginationOptionsData(maxPages: 5, stopWhenEmpty: true),
        ));

        $this->assertCount(1, $pages);
        $this->assertSame(1, $calls);
    }
}
