<?php

namespace App\Domain\Order\Aggregate;

use App\Domain\AggregateRoot;
use App\Domain\Order\ValueObject\OrderStatus;
use App\Domain\User\Aggregate\User;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class Order extends AggregateRoot
{
    public function __construct()
    {
    }

    public function getId(): Uuid
    {
        // TODO: Implement getId() method.
    }

    public function getUser(): User
    {

    }

    /**
     * @return Collection<OrderItem>
     */
    public function getItems(): Collection
    {

    }

    public function getStatus(): OrderStatus
    {

    }

    public function cancel(): void
    {

    }
}
