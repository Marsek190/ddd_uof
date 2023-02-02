<?php declare(strict_types=1);

namespace App\Domain\Auth;

use App\Domain\User\Aggregate\User;

interface AuthManagerInterface
{
    public function get(): User;
    public function set(User $user): void;
}
