<?php

namespace App\Domain\Cart\Policy;

use App\Domain\Auth\AuthManagerInterface;
use App\Domain\Cart\Aggregate\Cart;

final class CartPolicy
{
    public function __construct(private readonly AuthManagerInterface $authManager)
    {
    }

    public function can(Cart $cart): bool
    {
        $user = $this->authManager->get();

        return (string)$cart->getUser()->getId() === (string)$user->getId();
    }
}
