<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Read-only multipart stream. It emits field framing and the file handle
 * incrementally, while remaining rewindable so client v4 does not create an
 * additional pre-pipeline upload buffer.
 */
final class MultipartStream implements StreamInterface
{
    /** @var list<string|resource> */
    private array $parts;

    private int $part = 0;

    private int $partCount;

    private string $buffer = '';

    private int $position = 0;

    private readonly bool $seekable;

    private readonly int $size;

    /**
     * @param list<string> $fieldParts
     * @param resource $fileHandle
     */
    public function __construct(array $fieldParts, mixed $fileHandle, string $filePrefix, string $closingBoundary)
    {
        if (is_resource($fileHandle) === false) {
            throw new InvalidArgumentException('The upload file handle must be an open resource.');
        }

        $this->parts = [...$fieldParts, $filePrefix, $fileHandle, $closingBoundary];
        $this->partCount = count($this->parts);
        $metadata = stream_get_meta_data($fileHandle);
        $stat = fstat($fileHandle);

        if ($stat === false) {
            throw new RuntimeException('Unable to determine the upload file size.');
        }

        $fileSize = $stat['size'];
        $this->seekable = $metadata['seekable'] === true;
        $this->size = array_sum(array_map(
            static fn(string $part): int => strlen($part),
            [...$fieldParts, $filePrefix, $closingBoundary],
        )) + $fileSize;
    }

    public function __toString(): string
    {
        try {
            return $this->getContents();
        } catch (\Throwable) {
            return '';
        }
    }

    public function close(): void
    {
        $this->parts = [];
        $this->buffer = '';
        $this->part = 0;
        $this->partCount = 0;
    }

    public function detach(): mixed
    {
        $this->close();

        return null;
    }

    public function getSize(): ?int
    {
        return $this->seekable ? $this->size : null;
    }

    public function tell(): int
    {
        return $this->position;
    }

    public function eof(): bool
    {
        return $this->buffer === '' && $this->part >= $this->partCount;
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        if ($this->seekable === false) {
            throw new RuntimeException('Multipart upload stream is not seekable.');
        }

        $target = match ($whence) {
            SEEK_SET => $offset,
            SEEK_CUR => $this->position + $offset,
            SEEK_END => $this->size + $offset,
            default => throw new InvalidArgumentException('Invalid seek origin.'),
        };

        if ($target < 0 || $target > $this->size) {
            throw new RuntimeException('Unable to seek to the requested multipart stream position.');
        }

        $this->reset();
        $remaining = $target;

        while ($remaining > 0) {
            $chunk = $this->read(min(8192, $remaining));
            if ($chunk === '') {
                throw new RuntimeException('Unable to seek to the requested multipart stream position.');
            }

            $remaining -= strlen($chunk);
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return false;
    }

    public function write($string): int
    {
        unset($string);

        throw new RuntimeException('Multipart upload streams are read-only.');
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read($length): string
    {
        if ($length < 0) {
            throw new InvalidArgumentException('Read length must be a non-negative integer.');
        }

        if ($length === 0 || $this->eof()) {
            return '';
        }

        while (strlen($this->buffer) < $length && $this->part < $this->partCount) {
            $current = $this->parts[$this->part];

            if (is_string($current)) {
                $this->buffer .= $current;
                ++$this->part;

                continue;
            }

            $chunk = fread($current, max(1, $length - strlen($this->buffer)));
            if ($chunk === false) {
                throw new RuntimeException('Unable to read the upload file.');
            }

            if ($chunk === '') {
                ++$this->part;

                continue;
            }

            $this->buffer .= $chunk;
        }

        $result = substr($this->buffer, 0, $length);
        $this->buffer = substr($this->buffer, strlen($result));
        $this->position += strlen($result);

        return $result;
    }

    public function getContents(): string
    {
        $contents = '';

        while ($this->eof() === false) {
            $contents .= $this->read(8192);
        }

        return $contents;
    }

    public function getMetadata($key = null): mixed
    {
        return $key === null ? [] : null;
    }

    private function reset(): void
    {
        foreach ($this->parts as $part) {
            if (is_resource($part) && rewind($part) === false) {
                throw new RuntimeException('Unable to rewind the upload file.');
            }
        }

        $this->part = 0;
        $this->buffer = '';
        $this->position = 0;
    }
}
