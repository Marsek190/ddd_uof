<?php

namespace App\Domain\Order\Command;

use App\Domain\CommandInterface;

final class RefundOrderCommand implements CommandInterface
{
    public function __construct()
    {
    }
}
