<?php

namespace App\SharedKernel\Validation;

interface ValidatorInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data, array $rules): void;
}
