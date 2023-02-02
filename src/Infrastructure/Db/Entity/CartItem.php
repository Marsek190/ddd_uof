<?php

namespace App\Infrastructure\Db\Entity;

final class CartItem extends \App\Domain\Cart\Aggregate\CartItem implements EntityInterface
{
    public function __construct()
    {
    }

    public static function getTable(): string
    {
        return 'cart_items';
    }

    public function toArray(): array
    {
        return [
            'id' => (string)$this->id,
            'cart_id' => (string)$this->cartId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'created_at' => $this->createdAt->toDateString(),
            'updated_at' => $this->updatedAt->toDateString(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function hydrate(array $data, array $requiredFields = []): static
    {
        // TODO: Implement hydrate() method.
    }
}
