<?php

namespace App\Domain\Order\ValueObject;

enum OrderStatus: string
{
    case FORMED = 'formed';
    case PAID = 'paid';

    public function isPaid(): bool
    {
        return $this->value === self::PAID->value;
    }
}
