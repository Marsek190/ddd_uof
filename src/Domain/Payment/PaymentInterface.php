<?php

namespace App\Domain\Payment;

use App\Domain\Order\Aggregate\Order;

interface PaymentInterface
{
    public function getId(): int;
    public function getCode(): string;
    public function isActive(): bool;
    public function isAvailableFor(Order $order): bool;
}
