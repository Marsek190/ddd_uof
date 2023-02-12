<?php declare(strict_types=1);

namespace App\Domain\User\Aggregate;

use App\Domain\AggregateRoot;
use App\Domain\Cart\Aggregate\Cart;
use App\Domain\Loyalty\Aggregate\Loyalty;
use App\Domain\Order\Aggregate\Order;
use App\Domain\User\Event\UserAuthorizedEvent;
use App\Domain\User\ValueObject\Phone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ramsey\Uuid\UuidInterface;

class User extends AggregateRoot
{
    protected Loyalty $loyalty;
    protected readonly Collection $orders;
    protected readonly ?Cart $cart;
    protected Carbon $loginAt;

    public function __construct(
        protected readonly UuidInterface $id,
        protected readonly Phone $phone,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getLoyalty(): Loyalty
    {
        return $this->loyalty;
    }

    public function setLoyalty(Loyalty $loyalty): void
    {
        $this->loyalty = $loyalty;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    /**
     * @param Collection<Order> $orders
     * @return void
     */
    public function setOrders(Collection $orders): void
    {

    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    /**
     * @return Collection<Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function authorize(): void
    {
        $this->loginAt = Carbon::now();
        $this->raiseEvent(new UserAuthorizedEvent($this));
    }
}
