<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Pagination;

use Generator;
use JOOservices\Flickr\Api\Api;
use JOOservices\Flickr\Api\ApiCallOptions;
use JOOservices\Flickr\Dtos\Common\ApiResponseData;

/**
 * Lazy, strictly bounded paginator. It never retries and never sleeps; it
 * stops at `maxPages`, at Flickr's reported page total, or at an empty page.
 *
 * @param array<string, mixed> $baseParameters
 */
final class Paginator
{
    /**
     * @param array<string, mixed> $baseParameters
     */
    public function __construct(
        private readonly Api $api,
        private readonly string $method,
        private readonly array $baseParameters = [],
        private readonly ?ApiCallOptions $options = null,
    ) {
    }

    /**
     * @return Generator<int, ApiResponseData>
     */
    public function pages(PaginationOptions $options): Generator
    {
        $page = 1;

        while ($page <= $options->maxPages) {
            $response = $this->api->call($this->method, [
                ...$this->baseParameters,
                'per_page' => $options->perPage,
                'page' => $page,
            ], $this->options);

            yield $page => $response;

            $totalsPath = $options->itemsPath;
            array_pop($totalsPath);
            $totalPages = $response->mapAt(...$totalsPath)['pages'] ?? null;
            $totalPages = is_numeric($totalPages) ? (int) $totalPages : null;

            if ($options->stopWhenEmpty && $response->listAt(...$options->itemsPath) === []) {
                return;
            }

            if ($totalPages !== null && $page >= $totalPages) {
                return;
            }

            ++$page;
        }
    }
}
