<?php
declare(strict_types=1);

namespace App\Domain\Cart;

use App\Domain\Cart\Aggregate\Cart;

interface CartRelationLoaderInterface
{
    public function loadProducts(Cart $cart, array $nested = []): void;
    public function loadItems(Cart $cart, array $nested = []): void;
}
