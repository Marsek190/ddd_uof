<?php

use App\Domain\Order\Event\OrderPaidEvent;
use App\Domain\User\Event\UserAuthorizedEvent;
use App\Domain\User\Handler\UpgradeUserLoyaltyLevelHandler;
use App\Domain\User\Handler\UserAuthorizedEventHandler;

return [
    OrderPaidEvent::class => [
        [UpgradeUserLoyaltyLevelHandler::class, 'handle'],
    ],
    UserAuthorizedEvent::class => [
        [UserAuthorizedEventHandler::class, 'handle'],
    ],
];
