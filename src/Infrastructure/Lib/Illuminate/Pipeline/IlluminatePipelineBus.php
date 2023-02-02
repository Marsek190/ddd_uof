<?php

namespace App\Infrastructure\Lib\Illuminate\Pipeline;

use App\SharedKernel\PipelineBusInterface;
use App\SharedKernel\PipelineNotFoundException;
use Closure;
use JetBrains\PhpStorm\Pure;

final class IlluminatePipelineBus implements PipelineBusInterface
{
    /**
     * @param IlluminatePipelineHub $hub
     */
    public function __construct(private readonly IlluminatePipelineHub $hub)
    {
    }

    public function pipe(object $passable, string $pipeline): mixed
    {
        if (!$this->isRegistered($pipeline)) {
            throw new PipelineNotFoundException($passable);
        }

        return $this->hub->pipe($passable, $pipeline);
    }

    public function register(string $pipeline, Closure $callback): void
    {
        if ($this->isRegistered($pipeline)) {
            return;
        }

        $this->hub->pipeline($pipeline, $callback);
    }

    #[Pure]
    public function isRegistered(string $pipeline): bool
    {
        return isset($this->hub->getPipelines()[$pipeline]);
    }
}
