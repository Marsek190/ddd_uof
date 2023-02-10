<?php
declare(strict_types=1);

namespace App\Infrastructure\RelationLoader;

use App\Domain\Cart\Aggregate\Cart;
use App\Domain\Cart\CartRelationLoaderInterface;

final class CartRelationLoader implements CartRelationLoaderInterface
{
    public function loadProducts(Cart $cart): void
    {
    }

    public function loadItems(Cart $cart): void
    {
    }
}
