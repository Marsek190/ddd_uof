<?php

namespace App\Domain\Order\Policy;

use App\Domain\Auth\AuthManagerInterface;
use App\Domain\Order\Aggregate\Order;

class OrderPolicy
{
    public function __construct(private readonly AuthManagerInterface $authManager)
    {
    }

    public function can(Order $order): bool
    {
        $user = $this->authManager->get();

        return (string)$order->getUser()->getId() === (string)$user->getId();
    }
}
