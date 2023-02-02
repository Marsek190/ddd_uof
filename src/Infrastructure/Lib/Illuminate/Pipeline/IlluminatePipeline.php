<?php

namespace App\Infrastructure\Lib\Illuminate\Pipeline;

use Closure;
use DI\FactoryInterface;
use Illuminate\Pipeline\Pipeline;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;
use Throwable;

final class IlluminatePipeline extends Pipeline
{
    #[Pure]
    public function __construct(ContainerInterface&FactoryInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    /**
     * @throws Throwable
     */
    protected function carry(): Closure
    {
        return function (Closure $stack, callable|object|string $pipe): Closure {
            return function () use ($stack, $pipe): mixed {
                $args = [...func_get_args(), $stack];

                try {
                    if (is_callable($pipe)) {
                        return $pipe(...$args);
                    }

                    if (is_string($pipe)) {
                        $pipe = $this->container->make($pipe);
                    }

                    return $pipe->{$this->method}(...$args);
                } catch (Throwable $e) {
                    return $this->handleException(null, $e);
                }
            };
        };
    }
}
