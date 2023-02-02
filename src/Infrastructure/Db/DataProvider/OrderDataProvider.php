<?php

namespace App\Infrastructure\Db\DataProvider;

use App\Domain\CommandInterface;
use App\Domain\Order\Aggregate\Order;
use App\Domain\Order\Command\CancelOrderCommand;
use App\Domain\Order\Command\DeleteOrderCommand;
use App\Domain\Order\Command\RefundOrderCommand;
use App\Domain\Order\DataProvider\OrderDataProviderInterface;
use App\Domain\QueryInterface;
use App\Infrastructure\Db\Factory\QueryBuilderFactory;
use App\Infrastructure\Db\IdentityMap;
use App\Infrastructure\Db\Entity\User;
use App\SharedKernel\HydratorInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class OrderDataProvider implements OrderDataProviderInterface
{
    public function __construct(
        private readonly HydratorInterface $hydrator,
        private readonly QueryBuilderFactory $queryBuilderFactory,
        private readonly IdentityMap $identityMap,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getByQuery(QueryInterface $query): Collection
    {
        throw new InvalidArgumentException("{$query::class} is not supported.");
    }

    /**
     * @throws Exception
     */
    public function getByCommand(CommandInterface $command): ?Order
    {
        if ($command instanceof CancelOrderCommand) {
            return $this->getByCancelOrderCommand($command);
        } elseif ($command instanceof RefundOrderCommand) {
            return $this->getByRefundOrderCommand($command);
        } elseif ($command instanceof DeleteOrderCommand) {
            return $this->getByDeleteOrderCommand($command);
        }

        throw new InvalidArgumentException("{$command::class} is not supported.");
    }

    /**
     * @throws Exception
     */
    private function getByCancelOrderCommand(CancelOrderCommand $command): ?Order
    {
        $query = $this->queryBuilderFactory->createQueryBuilder()
            ->from('orders')
            ->select([
                'orders.id as order_id',
                'users.id as user_id',
                // ...
            ])
            ->innerJoin(
                fromAlias: '',
                join: '',
                alias: '',
                condition: '',
            );

        /** @var array $result */
        $result = $query->fetchAssociative();

        if (empty($result)) {
            return null;
        }

        // ...

        /** @var User $user */
        $user = $this->hydrator->hydrate(User::class, []);
        /** @var \App\Infrastructure\Db\Entity\Order $order */
        $order = $this->hydrator->hydrate(Order::class, [
            'user' => $user,
        ]);

        return $order;
    }

    private function getByRefundOrderCommand(RefundOrderCommand $command): ?Order
    {
        return null;
    }

    private function getByDeleteOrderCommand(DeleteOrderCommand $command): ?Order
    {
        return null;
    }

    /**
     * @throws Exception
     */
    private function createQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilderFactory->createQueryBuilder()
            ->from('orders')
            ->select([
                // some columns here
            ])
            ->innerJoin(
                fromAlias: 'orders',
                join: 'order_items',
                alias: 'order_items',
                condition: 'orders.id = order_items.order_id'
            );
    }
}
