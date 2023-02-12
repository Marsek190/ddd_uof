<?php

namespace App\Infrastructure\Db;

use Ramsey\Uuid\UuidInterface;

class IdentityMap
{
    /**
     * @var array<string, object>
     */
    private static array $instances = [];

    public function has(UuidInterface $uuid): bool
    {
        return isset(self::$instances[(string)$uuid]);
    }

    public function get(UuidInterface $uuid): object
    {
        return self::$instances[(string)$uuid];
    }

    public function set(UuidInterface $uuid, object $object): void
    {
        self::$instances[(string)$uuid] = clone $object;
    }
}
