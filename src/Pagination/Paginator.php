<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Pagination;

use JOOservices\Flickr\DTO\Common\ApiResponseData;
use JOOservices\Flickr\DTO\Common\PaginationOptionsData;

final class Paginator
{
    /**
     * @param  callable(int $page, ?int $perPage): ApiResponseData  $fetchPage
     * @param  null|callable(ApiResponseData): bool  $isEmpty
     * @return iterable<ApiResponseData>
     */
    public function pages(callable $fetchPage, ?PaginationOptionsData $pagination = null, ?callable $isEmpty = null): iterable
    {
        $pagination ??= new PaginationOptionsData;
        $page = $pagination->startPage;
        $pagesRead = 0;

        while ($pagination->maxPages === null || $pagesRead < $pagination->maxPages) {
            $response = $fetchPage($page, $pagination->perPage);

            yield $response;

            $pagesRead++;

            if ($pagination->stopWhenEmpty && ($isEmpty !== null ? $isEmpty($response) : $this->isEmpty($response))) {
                break;
            }

            if ($response->pagination === null || $page >= $response->pagination->pages) {
                break;
            }

            $page++;
        }
    }

    private function isEmpty(ApiResponseData $response): bool
    {
        foreach ($response->data as $value) {
            if (! is_array($value)) {
                continue;
            }

            foreach ($value as $nested) {
                if (is_array($nested) && $nested !== []) {
                    return false;
                }
            }
        }

        return true;
    }
}
