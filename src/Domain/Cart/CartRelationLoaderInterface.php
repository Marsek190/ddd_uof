<?php
declare(strict_types=1);

namespace App\Domain\Cart;

use App\Domain\Cart\Aggregate\Cart;

interface CartRelationLoaderInterface
{
    public function loadProducts(Cart $cart): void;
    public function loadItems(Cart $cart): void;
}
