<?php declare(strict_types=1);

namespace App\Domain\User\Handler;

use App\Domain\EntityManagerInterface;
use App\Domain\Loyalty\Aggregate\Loyalty;
use App\Domain\Order\Aggregate\Order;
use App\Domain\Order\Aggregate\OrderItem;
use App\Domain\Order\Event\OrderPaidEvent;
use App\Domain\User\UserRelationLoaderInterface;
use Illuminate\Support\Collection;
use Psr\EventDispatcher\EventDispatcherInterface;

/** @noinspection PhpUnused */
final class UpgradeUserLoyaltyLevelHandler
{
    private const UPGRADE_LEVELS = [
        2 => 1_000,
        5 => 10_000,
        7 => 100_000,
        15 => 500_000,
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly UserRelationLoaderInterface $userRelationLoader,
    ) {
    }

    public function handle(OrderPaidEvent $event): void
    {
        $user = $event->order->getUser();

        $this->userRelationLoader->loadOrders($user);
        $this->userRelationLoader->loadLoyalty($user);

        $orders = $user->getOrders();
        $loyalty = $user->getLoyalty();

        if ($this->isLoyaltyLevelMightBeUpgrade($loyalty, $orders) === false) {
            return;
        }

        $loyalty->upgrade();
        $user->setLoyalty($loyalty);

        $this->entityManager->persists($user);
        $this->entityManager->transactional(function () use ($user): void {
            foreach ($user->popEvents() as $event) {
                $this->dispatcher->dispatch($event);
            }
        });
    }

    private function calculateTotalOrdersPrice(Collection $orders): int
    {
        return $orders->reduce(static function (Order $order, int $totalOrdersPrice): int {
            $totalOrdersPrice += $order->getItems()->sum(fn (OrderItem $item): int => $item->getPrice());

            return $totalOrdersPrice;
        }, initial: 0);
    }

    private function isLoyaltyLevelMightBeUpgrade(Loyalty $loyalty, Collection $orders): bool
    {
        $paidOrders = $orders->filter(fn (Order $order): bool => $order->getStatus()->isPaid());
        $totalOrdersPrice = $this->calculateTotalOrdersPrice($paidOrders);
        $discountAmountInPercentage = $loyalty->getDiscountAmountInPercentage();
        $highestAbilityDiscount = (int)array_slice(array_keys(self::UPGRADE_LEVELS), -1)[0];

        if ($discountAmountInPercentage >= $highestAbilityDiscount) {
            return false;
        }

        return $totalOrdersPrice > self::UPGRADE_LEVELS[$discountAmountInPercentage]
            ?? current(self::UPGRADE_LEVELS);
    }
}
