<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\Exceptions\TokenStorageException;
use JsonException;

final class FileTokenStore implements FlickrTokenStoreContract
{
    public function __construct(private string $path) {}

    public function get(): ?AccessTokenData
    {
        if (! file_exists($this->path)) {
            return null;
        }

        if (! is_readable($this->path)) {
            throw new TokenStorageException('Token file is not readable.');
        }

        try {
            $decoded = json_decode((string) file_get_contents($this->path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new TokenStorageException('Token file contains invalid JSON.', 0, $exception);
        }

        if (! is_array($decoded)) {
            throw new TokenStorageException('Token file must contain a JSON object.');
        }

        return AccessTokenData::from($decoded);
    }

    public function put(AccessTokenData $token): void
    {
        $directory = dirname($this->path);

        if (! is_dir($directory) || ! is_writable($directory)) {
            throw new TokenStorageException('Token directory is not writable.');
        }

        $encoded = json_encode($token->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        if (file_put_contents($this->path, $encoded, LOCK_EX) === false) {
            throw new TokenStorageException('Failed to write token file.');
        }
    }

    public function forget(): void
    {
        if (file_exists($this->path) && ! unlink($this->path)) {
            throw new TokenStorageException('Failed to delete token file.');
        }
    }
}
