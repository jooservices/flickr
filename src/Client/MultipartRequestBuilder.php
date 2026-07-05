<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use JOOservices\Flickr\Exceptions\UploadException;

/**
 * @internal
 */
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

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new UploadException("Unable to open upload file at {$path}.");
        }

        $multipart[] = [
            'name' => 'photo',
            'contents' => $handle,
            'filename' => basename($path),
        ];

        return $multipart;
    }

    /**
     * @param  list<array{name: string, contents: mixed, filename?: string}>  $multipart
     */
    public function close(array $multipart): void
    {
        foreach ($multipart as $part) {
            if (is_resource($part['contents'])) {
                fclose($part['contents']);
            }
        }
    }
}
