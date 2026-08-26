<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Exceptions\UploadException;

final class FileValidator
{
    public function __construct(private readonly int $maxBytes)
    {
    }

    /** @return resource opened binary handle, closed by the caller */
    public function open(string $path): mixed
    {
        if (trim($path) === '') {
            throw new UploadException('The upload path must not be blank.');
        }

        $real = realpath($path);

        if ($real === false || is_file($real) === false || is_readable($real) === false) {
            throw new UploadException(sprintf('The upload target "%s" is not a readable regular file.', basename($path)));
        }

        $size = (int) filesize($real);

        if ($size <= 0) {
            throw new UploadException('The upload file is empty.');
        }

        if ($this->maxBytes > 0 && $size > $this->maxBytes) {
            throw new UploadException(sprintf('The upload file exceeds the configured %d byte limit.', $this->maxBytes));
        }

        $handle = fopen($real, 'rb');

        if ($handle === false) {
            throw new UploadException('The upload file could not be opened.');
        }

        return $handle;
    }
}
