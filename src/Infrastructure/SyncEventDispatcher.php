<?php

namespace App\Infrastructure;

use DI\FactoryInterface;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class SyncEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private readonly ContainerInterface&FactoryInterface $container,
        private readonly array $listen,
    ) {
        //$this->listen = require_once __DIR__ . './../../config/events.php';
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function dispatch(object $event): void
    {
        if (!isset($this->listen[$event::class])) {
            throw new InvalidArgumentException("Listener for {$event::class} was not found.");
        }

        /**
         * @var string $className
         * @var string $methodName
         */
        foreach ($this->listen[$event::class] as [$className, $methodName]) {
            /** @var object $handler */
            $handler = $this->container->make($className);

            call_user_func([$handler, $methodName], $event);
        }
    }
}
