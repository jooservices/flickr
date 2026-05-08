<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

final class UrlBuilder
{
    /**
     * @param  array<string, mixed>  $query
     */
    public function withQuery(string $url, array $query): string
    {
        if ($query === []) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.QueryString::build($query);
    }
}
