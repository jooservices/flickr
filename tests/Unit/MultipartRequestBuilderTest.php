<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use InvalidArgumentException;
use JOOservices\Flickr\Client\MultipartRequestBuilder;
use JOOservices\Flickr\Client\Psr17Factories;
use PHPUnit\Framework\TestCase;

final class MultipartRequestBuilderTest extends TestCase
{
    private function builder(): MultipartRequestBuilder
    {
        return new MultipartRequestBuilder(Psr17Factories::nyholm(), 'jooservices-test');
    }

    /** @return resource */
    private function handle(string $content = 'bytes')
    {
        $handle = fopen('php://temp', 'r+b');
        self::assertIsResource($handle);
        fwrite($handle, $content);
        rewind($handle);

        return $handle;
    }

    public function testRejectsNonResourceFileHandle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->builder()->build('https://up.flickr.com/', [], 'photo', 'a.jpg', $this->notAResource());
    }

    private function notAResource(): mixed
    {
        return 'not-a-resource';
    }

    public function testMultilineFieldValuesPreserveSignedBytes(): void
    {
        $handle = $this->handle();
        $value = \Faker\Factory::create()->sentence() . "\r\n" . \Faker\Factory::create()->sentence();
        $request = $this->builder()->build(
            'https://up.flickr.com/',
            ['description' => $value],
            'photo',
            'a.jpg',
            $handle,
        );

        $body = (string) $request->getBody();

        self::assertStringContainsString("name=\"description\"\r\n\r\n" . $value . "\r\n", $body);
        fclose($handle);
    }

    public function testFieldNameAndFilenameStripQuotesBackslashesAndNewlines(): void
    {
        $handle = $this->handle();
        $request = $this->builder()->build(
            'https://up.flickr.com/',
            [],
            'photo',
            "evil\"\\\r\nname.jpg",
            $handle,
        );

        $body = (string) $request->getBody();

        self::assertStringContainsString('filename="evilname.jpg"', $body);
        fclose($handle);
    }

    public function testContentTypeHeaderAdvertisesTheGeneratedBoundary(): void
    {
        $handle = $this->handle();
        $request = $this->builder()->build('https://up.flickr.com/', [], 'photo', 'a.jpg', $handle);

        self::assertMatchesRegularExpression(
            '/^multipart\/form-data; boundary=[0-9a-f]{32}$/',
            $request->getHeaderLine('Content-Type'),
        );
        fclose($handle);
    }
}
