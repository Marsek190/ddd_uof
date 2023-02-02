<?php

namespace App\Domain\Cart\DataProvider;

use App\Domain\Cart\Aggregate\Cart;
use App\Domain\Exception\AggregateNotFoundException;
use Ramsey\Uuid\UuidInterface;

interface CartDataProviderInterface
{
    /**
     * @throws AggregateNotFoundException
     */
    public function get(UuidInterface $id): ?Cart;

    public function getByUser(UuidInterface $userId): ?Cart;
}
