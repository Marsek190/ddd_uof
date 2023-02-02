<?php

namespace App\SharedKernel;

use RuntimeException;

final class PipelineNotFoundException extends RuntimeException
{
    /**
     * @param object $passable
     */
    public function __construct(object $passable)
    {
        parent::__construct("Pipeline for object {$passable::class} not found.");
    }
}
