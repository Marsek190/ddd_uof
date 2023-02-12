<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Aggregate\User;

interface UserRelationLoaderInterface
{
    /**
     * `$this->userRelationLoader->loadOrders($user, [OrderItem::class, Product::class])`
     *
     * @param array<class-string> $nested
     */
    public function loadOrders(User $user, array $nested = []): void;

    /**
     * `$this->userRelationLoader->loadCart($user, [CartItem::class, Product::class])`
     *
     * @param array<class-string> $nested
     */
    public function loadCart(User $user, array $nested = []): void;

    public function loadLoyalty(User $user): void;
}
