<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Auth;

use JOOservices\Flickr\Contracts\Auth\FlickrTokenStoreContract;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\Exceptions\TokenStorageException;
use JsonException;

final class EncryptedTokenStore implements FlickrTokenStoreContract
{
    public function __construct(
        private FlickrTokenStoreContract $inner,
        private string $encryptionKey,
    ) {
        if (strlen($this->encryptionKey) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new TokenStorageException('Encryption key must be '.SODIUM_CRYPTO_SECRETBOX_KEYBYTES.' bytes.');
        }
    }

    public function get(): ?AccessTokenData
    {
        $token = $this->inner->get();

        if ($token === null) {
            return null;
        }

        return $this->decrypt($token);
    }

    public function put(AccessTokenData $token): void
    {
        $this->inner->put($this->encrypt($token));
    }

    public function forget(): void
    {
        $this->inner->forget();
    }

    private function encrypt(AccessTokenData $token): AccessTokenData
    {
        try {
            $payload = json_encode($token->toArray(), JSON_THROW_ON_ERROR);
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = sodium_crypto_secretbox($payload, $nonce, $this->encryptionKey);

            return new AccessTokenData(
                oauthToken: base64_encode($nonce.$cipher),
                oauthTokenSecret: 'encrypted',
            );
        } catch (JsonException $exception) {
            throw new TokenStorageException('Failed to encrypt access token.', 0, $exception);
        }
    }

    private function decrypt(AccessTokenData $encrypted): AccessTokenData
    {
        if ($encrypted->oauthTokenSecret !== 'encrypted') {
            return $encrypted;
        }

        try {
            $decoded = base64_decode($encrypted->oauthToken, true);

            if ($decoded === false || strlen($decoded) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES) {
                throw new TokenStorageException('Encrypted token payload is invalid.');
            }

            $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipher = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $plain = sodium_crypto_secretbox_open($cipher, $nonce, $this->encryptionKey);

            if ($plain === false) {
                throw new TokenStorageException('Failed to decrypt access token.');
            }

            /** @var array<string, mixed> $data */
            $data = json_decode($plain, true, 512, JSON_THROW_ON_ERROR);

            return AccessTokenData::from($data);
        } catch (JsonException $exception) {
            throw new TokenStorageException('Failed to decrypt access token.', 0, $exception);
        }
    }
}
