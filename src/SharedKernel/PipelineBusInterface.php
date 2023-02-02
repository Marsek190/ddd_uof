<?php

namespace App\SharedKernel;

interface PipelineBusInterface
{
    public function pipe(object $passable, string $pipeline): mixed;
}
