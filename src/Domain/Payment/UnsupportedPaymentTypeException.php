<?php declare(strict_types=1);

namespace App\Domain\Payment;

use DomainException;

final class UnsupportedPaymentTypeException extends DomainException
{
    public function __construct(public readonly PaymentInterface $payment)
    {
        $error = '';

        parent::__construct($error);
    }
}
