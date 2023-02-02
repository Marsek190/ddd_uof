<?php declare(strict_types=1);

namespace App\Domain\Payment;

use DomainException;

final class InvalidPaymentStatusException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Получен неизвестный статус оплаты.');
    }
}
