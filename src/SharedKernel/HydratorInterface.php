<?php

namespace App\SharedKernel;

interface HydratorInterface
{
    public function hydrate(string $className, array $data): object;
}
