<?php

namespace App\Infrastructure\Http\Resource;

interface ResourceInterface
{
    public function getContent(): string;
}
