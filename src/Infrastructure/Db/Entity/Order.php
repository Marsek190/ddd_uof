<?php

namespace App\Infrastructure\Db\Entity;

final class Order extends \App\Domain\Order\Aggregate\Order implements EntityInterface
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

    /**
     * @inheritDoc
     */
    public static function hydrate(array $data, array $requiredFields = []): static
    {
        // TODO: Implement hydrate() method.
    }
}
