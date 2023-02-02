<?php

namespace App\Infrastructure\Db\Entity;

final class User extends \App\Domain\User\Aggregate\User implements EntityInterface
{
    public function __construct()
    {
    }

    public static function getTable(): string
    {
        // TODO: Implement getTable() method.
    }

    public function toArray(): array
    {
        // TODO: Implement toArray() method.
    }

    public static function hydrate(array $data, array $requiredFields = []): static
    {
        // TODO: Implement hydrate() method.
    }
}
