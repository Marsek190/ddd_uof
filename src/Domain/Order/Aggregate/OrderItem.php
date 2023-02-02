<?php

namespace App\Domain\Order\Aggregate;

use App\Domain\AggregateRoot;
use Ramsey\Uuid\Uuid;

class OrderItem extends AggregateRoot
{
    public function getId(): Uuid
    {
        // TODO: Implement getId() method.
    }

    public function getPrice(): int
    {

    }
}
