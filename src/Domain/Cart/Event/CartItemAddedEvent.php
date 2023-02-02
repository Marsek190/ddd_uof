<?php

namespace App\Domain\Cart\Event;

use App\Domain\Cart\Aggregate\Cart;
use App\Domain\Cart\Aggregate\CartItem;
use App\Domain\Event;

final class CartItemAddedEvent implements Event
{
    public function __construct(public readonly Cart $cart, public readonly CartItem $item)
    {
    }
}
