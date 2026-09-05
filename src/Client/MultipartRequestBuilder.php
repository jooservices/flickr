<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Client;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;

/**
 * Builds multipart/form-data bodies that read binary file contents only while
 * the HTTP client consumes the PSR-7 request body.
 */
final class MultipartRequestBuilder
{
    public function __construct(private readonly Psr17Factories $psr17, private readonly string $userAgent)
    {
    }

    /**
     * @param array<string, string|list<string>> $fields all OAuth-signed non-file fields
     * @param mixed $fileHandle validated as an open stream resource
     */
    public function build(
        string $uri,
        array $fields,
        string $fileField,
        string $filename,
        mixed $fileHandle,
        ?string $contentType = null,
    ): RequestInterface {
        if (is_resource($fileHandle) === false) {
            throw new InvalidArgumentException('The upload file handle must be an open resource.');
        }

        $boundary = bin2hex(random_bytes(16));
        return $this->psr17->request
            ->createRequest('POST', $uri)
            ->withBody(new MultipartStream(
                $this->fieldParts($boundary, $fields),
                $fileHandle,
                $this->filePrefix($boundary, $fileField, $filename, $contentType),
                "\r\n--{$boundary}--\r\n",
            ))
            ->withHeader('Content-Type', 'multipart/form-data; boundary=' . $boundary)
            ->withHeader('User-Agent', $this->userAgent);
    }

    /**
     * @param array<string, string|list<string>> $fields
     * @return list<string>
     */
    private function fieldParts(string $boundary, array $fields): array
    {
        $parts = [];

        foreach ($fields as $name => $value) {
            foreach (is_array($value) ? $value : [$value] as $single) {
                $parts[] = sprintf(
                    "--{$boundary}\r\nContent-Disposition: form-data; name=\"%s\"\r\n\r\n%s\r\n",
                    $this->escape($name),
                    $single,
                );
            }
        }

        return $parts;
    }

    private function filePrefix(
        string $boundary,
        string $fileField,
        string $filename,
        ?string $contentType,
    ): string {
        return sprintf(
            "--{$boundary}\r\nContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"\r\nContent-Type: %s\r\n\r\n",
            $this->escape($fileField),
            $this->escape($filename),
            $this->escape($contentType ?? 'application/octet-stream'),
        );
    }

    private function escape(string $value): string
    {
        return str_replace(['"', "\\", "\r", "\n"], '', $value);
    }
}
