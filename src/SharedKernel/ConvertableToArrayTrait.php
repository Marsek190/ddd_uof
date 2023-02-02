<?php declare(strict_types=1);

namespace App\SharedKernel;

trait ConvertableToArrayTrait
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
