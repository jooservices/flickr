<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Support;

use JOOservices\Flickr\Exceptions\UploadException;

/**
 * @internal
 */
final class FileValidator
{
    public function validateReadableFile(string $path, ?int $maxBytes = null): void
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

        $size = filesize($path);
        if ($size === 0 || $size === false) {
            throw new UploadException('Upload file must not be empty.');
        }

        if ($maxBytes !== null && $size > $maxBytes) {
            throw new UploadException(sprintf(
                'Upload file exceeds the account size limit (%d bytes > %d bytes).',
                $size,
                $maxBytes,
            ));
        }
    }
}
