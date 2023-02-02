<?php

namespace App\Domain\Order\Event;

use App\Domain\Event;
use App\Domain\Order\Aggregate\Order;

final class OrderCreatedEvent implements Event
{
    public function __construct(public readonly Order $order)
    {
    }
}
