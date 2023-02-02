<?php declare(strict_types=1);

namespace App\Infrastructure\Db\Entity;

use App\SharedKernel\Validation\Assert;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

final class Cart extends \App\Domain\Cart\Aggregate\Cart implements EntityInterface
{
    public function __construct()
    {
    }

    public static function getTable(): string
    {
        return 'cart';
    }

    public function toArray(): array
    {
        return [
            'id' => (string)$this->id,
            'user_id' => (string)$this->user->getId(),
            'created_at' => $this->createdAt->toDateString(),
            'updated_at' => $this->updatedAt->toDateString(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function hydrate(array $data, array $requiredFields = []): static
    {
        Assert::allKeysArePresentInArray($requiredFields ?: [
            'id',
            'user',
            'created_at',
            'updated_at',
            'items',
        ], $data);

        $cart = new self();
        $cart->id = Uuid::fromString($data['id']);
        $cart->user = User::hydrate($data['user']);
        $cart->createdAt = Carbon::parse($data['created_at']);
        $cart->updatedAt = Carbon::parse($data['updated_at']);
        $cart->items = Collection::make($data['items'])
            ->map(fn (array $data): CartItem => CartItem::hydrate($data));

        return $cart;
    }
}
