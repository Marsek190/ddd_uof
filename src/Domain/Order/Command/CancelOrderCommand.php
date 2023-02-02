<?php

namespace App\Domain\Order\Command;

use App\Domain\CommandInterface;
use Ramsey\Uuid\UuidInterface;

final class CancelOrderCommand implements CommandInterface
{
    public function __construct(public readonly UuidInterface $orderId)
    {
    }
}
