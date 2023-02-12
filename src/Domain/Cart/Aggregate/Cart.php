<?php

namespace App\Domain\Cart\Aggregate;

use App\Domain\AggregateRoot;
use App\Domain\Cart\Event\CartItemAddedEvent;
use App\Domain\Product\Aggregate\Product;
use App\Domain\User\Aggregate\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface;

class Cart extends AggregateRoot
{
    public function __construct(
        protected UuidInterface $id,
        protected User $user,
        protected Collection $items,
        protected Carbon $createdAt,
        protected Carbon $updatedAt,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Collection<CartItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /** @noinspection PhpUnused */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    /** @noinspection PhpUnused */
    public function addItem(CartItem $item): void
    {
        $itemId = (string)$item->getId();

        if ($this->items->offsetExists($itemId)) {
            return;
        }

        $this->items->offsetSet($itemId, $item);
        $this->raiseEvent(new CartItemAddedEvent($this, $item));
    }

    public function addProduct(Product $product): void
    {

    }
}
