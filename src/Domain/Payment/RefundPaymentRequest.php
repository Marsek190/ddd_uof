<?php declare(strict_types=1);

namespace App\Domain\Payment;

final class RefundPaymentRequest
{
    public function __construct(public readonly string $transactionId)
    {
    }
}
