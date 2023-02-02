<?php

namespace App\Domain\Auth\Exception;

use App\SharedKernel\PhrasePluralizer;

class VerificationCodeIsWrongException extends \DomainException
{
    public function __construct(int $retriesLeft)
    {
        $error = sprintf(
            'Неверный код. %s %s %s.',
            PhrasePluralizer::pluralize($retriesLeft, ['Осталась', 'Осталось', 'Осталось']),
            $retriesLeft,
            PhrasePluralizer::pluralize($retriesLeft, ['попытка', 'попытки', 'попыток']),
        );

        parent::__construct($error);
    }
}
