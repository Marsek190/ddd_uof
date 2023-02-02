<?php

namespace App\Domain\Order\Event;

use App\Domain\Event;
use App\Domain\Order\Aggregate\Order;

final class OrderPaidEvent implements Event
{
    public function __construct(public readonly Order $order)
    {
    }
}
