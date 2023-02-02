<?php

namespace App\Domain\User\Event;

use App\Domain\Event;
use App\Domain\User\Aggregate\User;

final class UserAuthorizedEvent implements Event
{
    public function __construct(public readonly User $user)
    {
    }
}
