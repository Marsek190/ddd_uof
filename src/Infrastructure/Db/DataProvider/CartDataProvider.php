<?php

namespace App\Infrastructure\Db\DataProvider;

use App\Domain\Cart\Aggregate\Cart;
use App\Domain\Cart\DataProvider\CartDataProviderInterface;
use App\Infrastructure\Db\Factory\QueryBuilderFactory;
use App\Infrastructure\Db\IdentityMap;
use App\SharedKernel\HydratorInterface;
use Ramsey\Uuid\UuidInterface;

final class CartDataProvider implements CartDataProviderInterface
{
    public function __construct(
        private readonly HydratorInterface $hydrator,
        private readonly QueryBuilderFactory $queryBuilderFactory,
        private readonly IdentityMap $identityMap,
    ) {
    }

    public function get(UuidInterface $id): ?Cart
    {
        // TODO: Implement get() method.
    }

    public function getByUser(UuidInterface $userId): ?Cart
    {
        // TODO: Implement getByUser() method.
    }
}
