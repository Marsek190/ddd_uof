<?php

namespace App\Domain\Loyalty\Aggregate;

use App\Domain\AggregateRoot;
use Ramsey\Uuid\UuidInterface;

class Loyalty extends AggregateRoot
{
    public function __construct(
        private readonly UuidInterface $id,
        private int $discountAmountInPercentage,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getDiscountAmountInPercentage(): int
    {
        return $this->discountAmountInPercentage;
    }

    public function upgrade(): void
    {
        $this->discountAmountInPercentage = 1;
    }
}
