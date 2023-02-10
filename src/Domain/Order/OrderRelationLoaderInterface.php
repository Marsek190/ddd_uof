<?php
declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Order\Aggregate\Order;

interface OrderRelationLoaderInterface
{
    public function loadProducts(Order $order): void;
    public function loadItems(Order $order): void;
}
