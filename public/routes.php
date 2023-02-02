<?php

use App\Infrastructure\Http\Controller\MainPageController;
use FastRoute\RouteCollector;

return function (RouteCollector $router): void {
    $router->get('/', MainPageController::class);

    $router->addGroup('/cart', function (RouteCollector $router): void {

    });
};
