<?php

namespace App\Infrastructure\Http\Resource;

abstract class JsonResource implements ResourceInterface
{
    public function getContent(): string
    {
        return json_encode($this->getData());
    }

    abstract protected function getData(): array;
}
