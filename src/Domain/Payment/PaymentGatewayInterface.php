<?php declare(strict_types=1);

namespace App\Domain\Payment;

interface PaymentGatewayInterface
{
    /**
     * @throws PaymentGatewayException
     */
    public function createPayment(PaymentRequest $request): PaymentResponse;
    public function getPaymentDetails(string $transactionId): PaymentResponse;
    public function cancelPayment(string $transactionId): PaymentResponse;
    public function refundPayment(RefundPaymentRequest $request): RefundPaymentResponse;
    public function isSatisfiedBy(PaymentInterface $payment): bool;
}
