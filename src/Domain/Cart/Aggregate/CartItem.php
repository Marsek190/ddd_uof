<?php

namespace App\Domain\Cart\Aggregate;

use App\Domain\AggregateRoot;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class CartItem extends AggregateRoot
{
    public function __construct(
        protected readonly Uuid $id,
        protected readonly Uuid $cartId,
        protected readonly int $quantity,
        protected readonly int $price,
        protected readonly Carbon $createdAt,
        protected readonly Carbon $updatedAt,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }
}
