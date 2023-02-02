<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\Payment\YooKassa;

use App\Domain\Payment\InvalidPaymentStatusException;
use App\Domain\Payment\PaymentResponse;
use App\Domain\Payment\PaymentStatus;
use App\Domain\Payment\RefundPaymentResponse;
use YooKassa\Model\PaymentStatus as YooKassaPaymentStatus;
use YooKassa\Request\Payments\AbstractPaymentResponse;
use YooKassa\Request\Refunds\CreateRefundResponse;

final class ResponseFactory
{
    /**
     * @throws InvalidPaymentStatusException
     */
    public function createPaymentResponse(AbstractPaymentResponse $yooKassaResponse): PaymentResponse
    {
        $transactionId = $yooKassaResponse->getId();
        $paymentStatus = match ($yooKassaResponse->status) {
            YooKassaPaymentStatus::PENDING => PaymentStatus::Pending,
            YooKassaPaymentStatus::SUCCEEDED => PaymentStatus::Success,
            YooKassaPaymentStatus::CANCELED => PaymentStatus::Cancel,
            default => throw new InvalidPaymentStatusException(),
        };
        $confirmationUrl = $yooKassaResponse->confirmation->getConfirmationUrl();
        $transactionDetails = $yooKassaResponse->toArray();

        return new PaymentResponse(
            $transactionId,
            $paymentStatus,
            $confirmationUrl,
            $transactionDetails
        );
    }

    public function createRefundResponse(CreateRefundResponse $yooKassaResponse): RefundPaymentResponse
    {
        $transactionId = $yooKassaResponse->getId();
        $paymentStatus = PaymentStatus::Refund;
        $transactionDetails = $yooKassaResponse->toArray();

        return new RefundPaymentResponse($transactionId, $paymentStatus, $transactionDetails);
    }
}
