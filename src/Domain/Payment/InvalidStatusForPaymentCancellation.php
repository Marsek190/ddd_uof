<?php declare(strict_types=1);

namespace App\Domain\Payment;

use DomainException;

final class InvalidStatusForPaymentCancellation extends DomainException
{
    public function __construct(public readonly PaymentStatus $paymentStatus)
    {
        $error = match ($paymentStatus) {
            PaymentStatus::Cancel => 'Нельзя отменить платеж в статусе "Отменен".',
            PaymentStatus::Success => 'Нельзя отменить платеж в статусе "Исполнен". Запросите возврат средств.',
            default => '',
        };

        parent::__construct($error);
    }
}
