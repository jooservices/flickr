<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Exceptions\UploadException;

final class FileValidator
{
    public function validateReadableFile(string $path): void
    {
        if ($path === '' || ! file_exists($path)) {
            throw new UploadException('Upload file does not exist.');
        }

        if (is_dir($path)) {
            throw new UploadException('Upload path must be a file, not a directory.');
        }

        if (! is_readable($path)) {
            throw new UploadException('Upload file is not readable.');
        }

        if (filesize($path) === 0) {
            throw new UploadException('Upload file must not be empty.');
        }
    }
}
