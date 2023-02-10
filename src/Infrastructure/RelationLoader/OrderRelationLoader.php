<?php
declare(strict_types=1);

namespace App\Infrastructure\RelationLoader;

use App\Domain\Order\Aggregate\Order;
use App\Domain\Order\OrderRelationLoaderInterface;

final class OrderRelationLoader implements OrderRelationLoaderInterface
{
    public function loadProducts(Order $order): void
    {
    }

    public function loadItems(Order $order): void
    {
    }
}
