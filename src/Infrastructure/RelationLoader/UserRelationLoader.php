<?php
declare(strict_types=1);

namespace App\Infrastructure\RelationLoader;

use App\Domain\Cart\Aggregate\CartItem;
use App\Domain\Cart\Aggregate\NullCart;
use App\Domain\Order\Aggregate\OrderItem;
use App\Domain\Product\Aggregate\Product;
use App\Domain\User\Aggregate\User;
use App\Domain\User\UserRelationLoaderInterface;
use App\Infrastructure\Db\Entity\Cart;
use App\Infrastructure\Db\Entity\Order;
use App\Infrastructure\Db\Factory\QueryBuilderFactory;
use App\Infrastructure\Db\IdentityMap;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class UserRelationLoader implements UserRelationLoaderInterface
{
    public function __construct(
        private readonly QueryBuilderFactory $queryBuilderFactory,
        private readonly IdentityMap $identityMap,
    ) {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function loadOrders(User $user, array $nested = []): void
    {
        $allowedNested = [
            OrderItem::class => static function (QueryBuilder $query): void {},
            Product::class => static function (QueryBuilder $query): void {},
        ];

        $query = $this->queryBuilderFactory->createQueryBuilder();

        /** @var class-string $className */
        foreach ($nested as $className) {
            if (isset($allowedNested[$className])) {
                $loader = $allowedNested[$className];

                call_user_func($loader, $query);
            }
        }

        $resultsGroupedByOrderId = (new Collection($query->fetchAllAssociative()))
            ->groupBy('order_id');

        if ($resultsGroupedByOrderId->isEmpty()) {
            $user->setOrders(Collection::empty());

            return;
        }

        $orders = $resultsGroupedByOrderId->map(static function (array $rows): Order {
            /** @var array[] $rows */
            $rows = array_values($rows);

            $order = Order::hydrate($rows[0]);

            foreach ($rows as $data) {
                try {
                    $orderItem = \App\Infrastructure\Db\Entity\OrderItem::hydrate(
                        data: $data,
                        requiredFields: [],
                    );
                    $order->addItem($orderItem);
                } catch (InvalidArgumentException) {
                }

                try {
                    $product = \App\Infrastructure\Db\Entity\Product::hydrate(
                        data: $data,
                        requiredFields: [],
                    );
                    $order->addProduct($product);
                } catch (InvalidArgumentException) {
                }
            }

            return $order;
        });

        $user->setOrders($orders);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function loadCart(User $user, array $nested = []): void
    {
        $allowedNested = [
            CartItem::class => static function (QueryBuilder $query): void {
                $query->addSelect([
                    '',
                ])->join('', '', '', '');
            },
            Product::class => static function (QueryBuilder $query): void {
                $query->addSelect([
                    '',
                ])->join('', '', '', '');
            },
        ];

        $query = $this->queryBuilderFactory->createQueryBuilder();

        /** @var class-string $className */
        foreach ($nested as $className) {
            if (isset($allowedNested[$className])) {
                $loader = $allowedNested[$className];

                call_user_func($loader, $query);
            }
        }

        /** @var array[] $results */
        $results = $query->fetchAllAssociative();

        if (empty($results)) {
            $user->setCart(new NullCart());

            return;
        }

        $cart = Cart::hydrate($results[0]);

        foreach ($results as $data) {
            try {
                $cartItem = \App\Infrastructure\Db\Entity\CartItem::hydrate(
                    data: $data,
                    requiredFields: [],
                );
                $cart->addItem($cartItem);
            } catch (InvalidArgumentException) {
            }

            try {
                $product = \App\Infrastructure\Db\Entity\Product::hydrate(
                    data: $data,
                    requiredFields: [],
                );
                $cart->addProduct($product);
            } catch (InvalidArgumentException) {
            }
        }

        $user->setCart($cart);

        $this->identityMap->set($user->getId(), $user);
    }
}
