<?php

namespace App\Infrastructure\Db\EntityManager;

use App\Domain\AggregateRoot;
use App\Infrastructure\Db\Entity\Cart;
use App\Infrastructure\Db\Entity\CartItem;
use DateTimeInterface;
use Illuminate\Support\Collection;

final class CartEntityManager extends EntityManager
{
    protected string $entityClassName = Cart::class;

    public function remove(AggregateRoot $aggregateRoot): void
    {
        $callback = function (): void {
            /** @var Cart $aggregateRoot */

            $items = $aggregateRoot->getItems();
            $removedIDs = [...$items->map(fn (CartItem $item): string => (string)$item->getId())];

            $this->dbAdapter->deleteBatch(CartItem::getTable(), $removedIDs);

            parent::remove($aggregateRoot);
        };

        $this->transactional($callback);
    }

    protected function isSatisfiedBy(AggregateRoot $aggregateRoot): bool
    {
        return $aggregateRoot instanceof \App\Domain\Cart\Aggregate\Cart;
    }

    protected function insertAggregateRoot(AggregateRoot $aggregateRoot): void
    {
        $callback = function (): void {
            /** @var AggregateRoot|Cart $aggregateRoot */

            $this->dbAdapter->insertOne($aggregateRoot->getTable(), $aggregateRoot->toArray());

            /** @var array<array<string, mixed>> $batch */
            $batch = [...$aggregateRoot->getItems()->map(fn (CartItem $item): array => $item->toArray())];

            $this->dbAdapter->insertBatch(CartItem::getTable(), $batch);
        };

        $this->transactional($callback);
    }

    protected function updateAggregateRoot(AggregateRoot $initial, AggregateRoot $updated): void
    {
        $callback = function (): void {
            /** @var AggregateRoot|Cart $initial */
            /** @var AggregateRoot|Cart $updated */

            $this->updateAggregateRootItems($initial->getItems(), $updated->getItems());

            $data = [];

            if ($initial->getUpdatedAt() != $updated->getUpdatedAt()) {
                $data['updated_at'] = $updated->getUpdatedAt()->format(DateTimeInterface::RFC3339);
            }

            if ((string)$initial->getUser()->getId() !== (string)$updated->getUser()->getId()) {
                $data['user_id'] = (string)$updated->getUser()->getId();
            }

            if (!empty($data)) {
                $this->dbAdapter->updateOne(Cart::getTable(), (string)$initial->getId(), $data);
            }
        };

        $this->transactional($callback);
    }

    /**
     * @param Collection<CartItem> $initial
     * @param Collection<CartItem> $updated
     */
    private function updateAggregateRootItems(Collection $initial, Collection $updated): void
    {
        $table = CartItem::getTable();
        $updatedQuantityItems = $updated->filter(
            function (CartItem $item) use ($initial): bool {
                return isset($initial[(string)$item->getId()]) &&
                    $initial[(string)$item->getId()]->getQuantity() !== $item->getQuantity();
            }
        );

        if ($updatedQuantityItems->isNotEmpty()) {
            $where = [];
            $replacements = [];

            /** @var CartItem $updatedQuantityItem */
            foreach ($updatedQuantityItems as $updatedQuantityItem) {
                $where[] = [
                    'id' => (string)$updatedQuantityItem->getId(),
                ];
                $replacements[] = [
                    'quantity' => $updatedQuantityItem->getQuantity(),
                ];
            }

            $this->dbAdapter->updateBatch($table, $where, $replacements);

            //$query = '';
            //$bindings = [];

            //foreach ($updatedQuantityItems as $updatedQuantityItem) {
            //$query .= 'UPDATE `some_table_name` WHERE `id` = ? SET `quantity` = ?;' . PHP_EOL;
            //$bindings[] = (string)$updatedQuantityItem->getId();
            //$bindings[] = $updatedQuantityItem->getQuantity();
            //}

            //$this->dbAdapter->getPDODriver()->query($query, $bindings);
        }

        if ($initial->count() === $updated->count()) {
            return;
        }

        if ($initial->count() > $updated->count()) {
            $removed = $initial->diff($updated);
            /** @var string[] $removedIDs */
            $removedIDs = [...$removed->keys()];
            // $primaryKeys = ...

            $this->dbAdapter->deleteBatch($table, $removedIDs);

            return;
        }

        $appended = $updated->diff($initial)->values();
        /** @var array<array<string, mixed>> $batch */
        $batch = [...$appended->map(fn (CartItem $item): array => $item->toArray())];

        $this->dbAdapter->insertBatch($table, $batch);
    }
}
