<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Client\MultipartStream;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MultipartStreamTest extends TestCase
{
    /** @var resource */
    private $handle;

    protected function setUp(): void
    {
        $handle = fopen('php://temp', 'r+b');
        self::assertIsResource($handle);
        /** @var resource $handle */
        $this->handle = $handle;
        fwrite($this->handle, 'FILEBYTES');
        rewind($this->handle);
    }

    protected function tearDown(): void
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    private function stream(): MultipartStream
    {
        return new MultipartStream(
            ["--b\r\nContent-Disposition: form-data; name=\"a\"\r\n\r\n1\r\n"],
            $this->handle,
            "--b\r\nContent-Disposition: form-data; name=\"photo\"\r\n\r\n",
            "\r\n--b--\r\n",
        );
    }

    public function testRejectsNonResourceFileHandle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MultipartStream([], $this->notAResource(), '', '');
    }

    private function notAResource(): mixed
    {
        return 'not-a-resource';
    }

    public function testGetContentsConcatenatesAllPartsInOrder(): void
    {
        $stream = $this->stream();

        $expected = "--b\r\nContent-Disposition: form-data; name=\"a\"\r\n\r\n1\r\n"
            . "--b\r\nContent-Disposition: form-data; name=\"photo\"\r\n\r\n"
            . 'FILEBYTES'
            . "\r\n--b--\r\n";

        self::assertSame($expected, $stream->getContents());
        self::assertTrue($stream->eof());
        self::assertSame(strlen($expected), $stream->tell());
    }

    public function testToStringNeverThrows(): void
    {
        self::assertStringContainsString('FILEBYTES', (string) $this->stream());
    }

    public function testStringConversionRewindsAfterPartialAndCompleteReads(): void
    {
        $stream = $this->stream();
        $expected = $stream->getContents();
        self::assertSame($expected, (string) $stream);
        $stream->rewind();
        $stream->read(5);
        self::assertSame($expected, (string) $stream);
    }

    public function testGetSizeMatchesActualContentLength(): void
    {
        $stream = $this->stream();
        $expectedSize = $stream->getSize();
        $actual = $stream->getContents();

        self::assertNotNull($expectedSize);
        self::assertSame(strlen($actual), $expectedSize);
    }

    public function testReadZeroLengthReturnsEmptyString(): void
    {
        self::assertSame('', $this->stream()->read(0));
    }

    public function testReadRejectsNegativeLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->stream()->read(-1);
    }

    public function testIsWritableIsFalseAndWriteThrows(): void
    {
        $stream = $this->stream();
        self::assertFalse($stream->isWritable());

        $this->expectException(RuntimeException::class);
        $stream->write('x');
    }

    public function testIsReadableIsTrue(): void
    {
        self::assertTrue($this->stream()->isReadable());
    }

    public function testSeekableStreamCanRewindAndRereadIdentically(): void
    {
        $stream = $this->stream();
        $first = $stream->getContents();

        self::assertTrue($stream->isSeekable());
        $stream->rewind();
        $second = $stream->getContents();

        self::assertSame($first, $second);
    }

    public function testSeekToMiddleThenReadsRemainder(): void
    {
        $stream = $this->stream();
        $full = $stream->getContents();
        $stream->rewind();

        $stream->seek(5);
        self::assertSame(5, $stream->tell());
        self::assertSame(substr($full, 5), $stream->getContents());
    }

    public function testSeekCurAndEndOrigins(): void
    {
        $stream = $this->stream();
        $full = $stream->getContents();
        $stream->rewind();

        $stream->seek(2);
        $stream->seek(3, SEEK_CUR);
        self::assertSame(5, $stream->tell());

        $stream->seek(-1, SEEK_END);
        self::assertSame(strlen($full) - 1, $stream->tell());
    }

    public function testSeekRejectsInvalidOrigin(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->stream()->seek(0, 99);
    }

    public function testSeekPastEndThrows(): void
    {
        $stream = $this->stream();
        $size = $stream->getSize();
        self::assertNotNull($size);

        $this->expectException(RuntimeException::class);
        $stream->seek($size + 100);
    }

    public function testSeekBeforeStartThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->stream()->seek(-1);
    }

    public function testNonSeekableStreamRejectsSeek(): void
    {
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        self::assertIsArray($sockets);
        [$readEnd, $writeEnd] = $sockets;

        fwrite($writeEnd, 'x');
        fclose($writeEnd);

        $stream = new MultipartStream([], $readEnd, '', '');
        self::assertFalse($stream->isSeekable());
        self::assertNull($stream->getSize());

        try {
            $this->expectException(RuntimeException::class);
            $stream->seek(0);
        } finally {
            fclose($readEnd);
        }
    }

    public function testCloseAndDetachResetState(): void
    {
        $stream = $this->stream();
        $stream->close();

        self::assertTrue($stream->eof());
        self::assertFalse($stream->isReadable());
        self::assertFalse($stream->isSeekable());
        self::assertNull($stream->getSize());
        self::assertFalse(is_resource($this->handle));
        self::assertSame('', (string) $stream);
        $this->expectException(RuntimeException::class);
        $stream->getContents();
    }

    public function testDetachReturnsNull(): void
    {
        $stream = $this->stream();
        self::assertNull($stream->detach());
        self::assertFalse($stream->isReadable());
        self::assertTrue(is_resource($this->handle));
    }

    public function testGetMetadataReturnsEmptyArrayOrNull(): void
    {
        $stream = $this->stream();

        self::assertSame([], $stream->getMetadata());
        self::assertNull($stream->getMetadata('anything'));
    }
}
