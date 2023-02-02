<?php

namespace App\Infrastructure\Db;

use Ramsey\Uuid\Uuid;

class IdentityMap
{
    /**
     * @var array<string, object>
     */
    private static array $instances = [];

    public function has(Uuid $uuid): bool
    {
        return isset(self::$instances[(string)$uuid]);
    }

    public function get(Uuid $uuid): object
    {
        return self::$instances[(string)$uuid];
    }

    public function set(Uuid $uuid, object $object): void
    {
        self::$instances[(string)$uuid] = clone $object;
    }
}
