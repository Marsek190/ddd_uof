<?php declare(strict_types=1);

namespace App\Domain\Order\Handler;

use App\Domain\EntityManagerInterface;
use App\Domain\Order\Command\CancelOrderCommand;
use App\Domain\Order\DataProvider\OrderDataProviderInterface;
use App\Domain\Order\Policy\OrderPolicy;
use DomainException;
use Psr\EventDispatcher\EventDispatcherInterface;

final class CancelOrderHandler
{
    public function __construct(
        private readonly OrderDataProviderInterface $orderDataProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly OrderPolicy $orderPolicy,
    ) {
    }

    /**
     * @throws DomainException
     */
    public function handle(CancelOrderCommand $command): void
    {
        $order = $this->orderDataProvider->getByCommand($command);

        if (!$this->orderPolicy->can($order)) {
            throw new DomainException('');
        }

        $order->cancel();

        $this->entityManager->persists($order);
        $this->entityManager->transactional(function () use ($order): void {
            foreach ($order->popEvents() as $event) {
                $this->dispatcher->dispatch($event);
            }
        });
    }
}
