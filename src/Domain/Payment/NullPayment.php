<?php

namespace App\Domain\Payment;

use App\Domain\Order\Aggregate\Order;

final class NullPayment implements PaymentInterface
{
    public function getId(): int
    {
        return -1;
    }

    public function getCode(): string
    {
        return '';
    }

    public function isActive(): bool
    {
        return true;
    }

    public function isAvailableFor(Order $order): bool
    {
        return false;
    }
}
