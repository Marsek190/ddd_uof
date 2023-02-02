<?php declare(strict_types=1);

namespace App\Domain\Payment;

use DomainException;

final class InvalidStatusForRefundPayment extends DomainException
{
    public function __construct(public readonly PaymentStatus $paymentStatus)
    {
        $error = '';

        parent::__construct($error);
    }
}
