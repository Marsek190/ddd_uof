<?php

namespace App\Infrastructure\Lib\Illuminate;

use App\SharedKernel\Validation\ValidatorInterface;
use Illuminate\Support\Facades\Validator;

final class IlluminateValidator implements ValidatorInterface
{
    public function validate(array $data, array $rules): void
    {
        Validator::validate($data, $rules);
    }
}
