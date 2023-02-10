<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Aggregate\User;

interface UserRelationLoaderInterface
{
    public function loadOrders(User $user): void;
    public function loadCart(User $user): void;
}
