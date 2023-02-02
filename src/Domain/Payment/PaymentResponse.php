<?php declare(strict_types=1);

namespace App\Domain\Payment;

final class PaymentResponse
{
    public function __construct(
        public readonly string $transactionId,
        public readonly PaymentStatus $status,
        public readonly string $confirmationUrl,
        public readonly array $transactionDetails,
    ) {
    }
}
