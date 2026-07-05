<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Tests\Unit;

use JOOservices\Flickr\Auth\EncryptedTokenStore;
use JOOservices\Flickr\Auth\InMemoryTokenStore;
use JOOservices\Flickr\DTO\Auth\AccessTokenData;
use JOOservices\Flickr\Exceptions\TokenStorageException;
use JOOservices\Flickr\Tests\TestCase;

final class EncryptedTokenStoreTest extends TestCase
{
    public function test_encrypts_and_decrypts_access_tokens(): void
    {
        $key = str_repeat('a', SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        $inner = new InMemoryTokenStore;
        $store = new EncryptedTokenStore($inner, $key);
        $token = new AccessTokenData('oauth-token', 'oauth-secret', 'user', 'username');

        $store->put($token);

        $stored = $inner->get();
        $this->assertNotNull($stored);
        $this->assertSame('encrypted', $stored->oauthTokenSecret);

        $this->assertEquals($token, $store->get());
    }

    public function test_rejects_invalid_encryption_key_length(): void
    {
        $this->expectException(TokenStorageException::class);
        new EncryptedTokenStore(new InMemoryTokenStore, 'short-key');
    }
}
