**Registration new pipeline at pipeline hub**

1. Declare new definition in `di.php` as follows below:

```
'pipeline.some_pipeline_name' => function (Container $c): PipelineBusInterface {
        /** @var IlluminatePipelineBus $pipelineBus */
        $pipelineBus = $c->get(PipelineBusInterface::class);
        $callback = function (Pipeline $pipeline, object $passable): Order {
            return $pipeline->send($passable)
                ->through([
                    SomePipe1::class,
                    SomePipe2::class,
                    SomePipe3::class,
                    // etc...
                ])
                ->thenReturn();
        };

        $pipelineBus->register('some_pipeline_name', $callback);

        return $pipelineBus;
    },
```

2. Create new class and inject interface `PipelineBusInterface` at him:

```
public function __construct(private readonly PipelineBusInterface $pipelineBus) {
}

```

3. Finally, add method at the recently created class aimed passing your object to pipeline bus:

```
public function someMethod(): mixed {
    $passable = ...;
    
    return $this->pipelineBus->pipe($passable, 'some_pipeline_name');
}
```
