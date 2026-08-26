<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

/**
 * Optional helper for building upload/set visibility parameters. The
 * registry-driven services accept raw scalars directly; this exists purely
 * so callers who want compile-time safety for Flickr's visibility vocabulary
 * do not have to memorize the `is_public`/`is_friend`/`is_family` triplet.
 */
enum Privacy: string
{
    case Public = 'public';
    case Private = 'private';
    case Friends = 'friends';
    case Family = 'family';
    case FriendsAndFamily = 'friends_and_family';

    /**
     * @return array{is_public: int, is_friend: int, is_family: int}
     */
    public function uploadFields(): array
    {
        return match ($this) {
            self::Public => ['is_public' => 1, 'is_friend' => 0, 'is_family' => 0],
            self::Private => ['is_public' => 0, 'is_friend' => 0, 'is_family' => 0],
            self::Friends => ['is_public' => 0, 'is_friend' => 1, 'is_family' => 0],
            self::Family => ['is_public' => 0, 'is_friend' => 0, 'is_family' => 1],
            self::FriendsAndFamily => ['is_public' => 0, 'is_friend' => 1, 'is_family' => 1],
        };
    }
}
