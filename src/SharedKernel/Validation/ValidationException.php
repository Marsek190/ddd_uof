<?php

namespace App\SharedKernel\Validation;

use InvalidArgumentException;

final class ValidationException extends InvalidArgumentException
{
    public function getData(): array
    {

    }
}
