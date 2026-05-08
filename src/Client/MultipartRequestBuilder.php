<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

final class MultipartRequestBuilder
{
    /**
     * @param  array<string, mixed>  $parameters
     * @return list<array{name: string, contents: mixed, filename?: string}>
     */
    public function build(string $path, array $parameters): array
    {
        $multipart = [];

        foreach ($parameters as $name => $value) {
            $multipart[] = ['name' => $name, 'contents' => (string) $value];
        }

        $multipart[] = [
            'name' => 'photo',
            'contents' => fopen($path, 'rb'),
            'filename' => basename($path),
        ];

        return $multipart;
    }
}
