<?php

namespace App\Domain\Payment;

use App\Domain\Order\Aggregate\Order;

final class YooKassaPayment implements PaymentInterface
{
    public function getId(): int
    {
        // TODO: Implement getId() method.
    }

    public function getCode(): string
    {
        // TODO: Implement getCode() method.
    }

    public function isActive(): bool
    {
        // TODO: Implement isActive() method.
    }

    public function isAvailableFor(Order $order): bool
    {
        // TODO: Implement isAvailableFor() method.
    }
}
