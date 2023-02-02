<?php declare(strict_types=1);

namespace App\Domain\Payment;

final class RefundPaymentResponse
{
    public function __construct(
        public readonly string $transactionId,
        public readonly PaymentStatus $status,
        public readonly array $transactionDetails,
    ) {
    }
}
