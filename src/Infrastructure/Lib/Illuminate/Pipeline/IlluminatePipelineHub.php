<?php

namespace App\Infrastructure\Lib\Illuminate\Pipeline;

use Illuminate\Pipeline\Hub;
use JetBrains\PhpStorm\Pure;
use Psr\Container\ContainerInterface;

final class IlluminatePipelineHub extends Hub
{
    /**
     * @param ContainerInterface $container
     * @param IlluminatePipeline $pipeline
     */
    #[Pure]
    public function __construct(ContainerInterface $container, private readonly IlluminatePipeline $pipeline)
    {
        parent::__construct();

        $this->container = $container;
    }

    /**
     * @param object $object
     * @param string|null $pipeline
     *
     * @return mixed
     */
    public function pipe($object, $pipeline = null): mixed
    {
        $pipeline = $pipeline ?: 'default';

        return call_user_func_array(
            $this->pipelines[$pipeline],
            [$this->pipeline, $object]
        );
    }

    public function getPipelines(): array
    {
        return $this->pipelines;
    }
}
