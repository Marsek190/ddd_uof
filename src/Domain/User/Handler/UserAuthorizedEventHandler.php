<?php declare(strict_types=1);

namespace App\Domain\User\Handler;

use App\Domain\User\Event\UserAuthorizedEvent;

final class UserAuthorizedEventHandler
{
    public function __construct()
    {
    }

    public function handle(UserAuthorizedEvent $event): void
    {

    }
}
