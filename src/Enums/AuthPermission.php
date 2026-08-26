<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum AuthPermission: string
{
    case None = 'none';
    case Read = 'read';
    case Write = 'write';
    case Delete = 'delete';

    public function satisfies(self $required): bool
    {
        return $this->rank() >= $required->rank();
    }

    private function rank(): int
    {
        return match ($this) {
            self::None => 0,
            self::Read => 1,
            self::Write => 2,
            self::Delete => 3,
        };
    }
}
