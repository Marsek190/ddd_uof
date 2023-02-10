<?php
declare(strict_types=1);

namespace App\Infrastructure\RelationLoader;

use App\Domain\User\Aggregate\User;
use App\Domain\User\UserRelationLoaderInterface;

final class UserRelationLoader implements UserRelationLoaderInterface
{
    public function loadOrders(User $user): void
    {
    }

    public function loadCart(User $user): void
    {
    }
}
