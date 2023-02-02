<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\Payment\YooKassa;

use App\Domain\Payment\InvalidStatusForPaymentCancellation;
use App\Domain\Payment\InvalidStatusForRefundPayment;
use App\Domain\Payment\NullPayment;
use App\Domain\Payment\PaymentInterface;
use App\Domain\Payment\PaymentGatewayException;
use App\Domain\Payment\PaymentGatewayInterface;
use App\Domain\Payment\PaymentRequest;
use App\Domain\Payment\PaymentResponse;
use App\Domain\Payment\PaymentStatus;
use App\Domain\Payment\RefundPaymentRequest;
use App\Domain\Payment\RefundPaymentResponse;
use App\Domain\Payment\UnsupportedPaymentTypeException;
use Exception;
use YooKassa\Client as YooKassaClient;
use YooKassa\Model\ConfirmationType;
use YooKassa\Model\CurrencyCode;
use YooKassa\Model\Receipt\PaymentMode;
use YooKassa\Model\Receipt\PaymentSubject;
use YooKassa\Request\Payments\CreatePaymentRequest;
use YooKassa\Request\Refunds\CreateRefundRequest;

final class YooKassaPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var string
     */
    private const REDIRECT_URL_IF_TRANSACTION_SUCCEEDS = '';

    public function __construct(
        private readonly YooKassaClient $yooKassaClient,
        private readonly ErrorHandler $errorHandler,
        private readonly ResponseFactory $responseFactory,
        YooKassaApiConfig $yooKassaApiConfig,
    ) {
        $this->yooKassaClient->setAuth($yooKassaApiConfig->shopId, $yooKassaApiConfig->apiToken);
    }

    /**
     * @throws PaymentGatewayException
     * @throws UnsupportedPaymentTypeException
     */
    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        $transactionAmount = (string)$request->transactionAmount;

        $builder = CreatePaymentRequest::builder();
        $builder->setAmount($transactionAmount)->setCurrency(CurrencyCode::RUB);
        $builder->setCapture(true);
        $builder->setDescription('Тестовая оплата');

        $builder->setConfirmation([
            'type' => ConfirmationType::REDIRECT,
            'returnUrl' => $request->redirectUrlIfTransactionSucceeds
                ?? self::REDIRECT_URL_IF_TRANSACTION_SUCCEEDS,
        ]);

        $builder->setReceiptPhone((string)$request->user->getPhone());

        $builder->addReceiptItem(
            title: 'Абонентская плата',
            price: $transactionAmount,
            quantity: 1.0,
            vatCode: 1,
            paymentMode: PaymentMode::FULL_PAYMENT,
            paymentSubject: PaymentSubject::SERVICE,
        );

        try {
            $yooKassaRequest = $builder->build();
            $yooKassaResponse = $this->yooKassaClient->createPayment($yooKassaRequest);

            return $this->responseFactory->createPaymentResponse($yooKassaResponse);
        } catch (Exception $exception) {
            $error = $this->errorHandler->handle($exception);

            throw new PaymentGatewayException($error);
        }
    }

    /**
     * @throws PaymentGatewayException
     */
    public function getPaymentDetails(string $transactionId): PaymentResponse
    {
        try {
            $yooKassaResponse = $this->yooKassaClient->getPaymentInfo($transactionId);

            return $this->responseFactory->createPaymentResponse($yooKassaResponse);
        } catch (Exception $exception) {
            $error = $this->errorHandler->handle($exception);

            throw new PaymentGatewayException($error);
        }
    }

    /**
     * @throws PaymentGatewayException
     * @throws InvalidStatusForPaymentCancellation
     */
    public function cancelPayment(string $transactionId): PaymentResponse
    {
        $paymentStatus = $this->getPaymentDetails($transactionId)->status;

        if ($paymentStatus !== PaymentStatus::Pending) {
            throw new InvalidStatusForPaymentCancellation($paymentStatus);
        }

        try {
            $yooKassaResponse = $this->yooKassaClient->cancelPayment($transactionId);

            return $this->responseFactory->createPaymentResponse($yooKassaResponse);
        } catch (Exception $exception) {
            $error = $this->errorHandler->handle($exception);

            throw new PaymentGatewayException($error);
        }
    }

    /**
     * @throws PaymentGatewayException
     */
    public function refundPayment(RefundPaymentRequest $request): RefundPaymentResponse
    {
        $transactionId = $request->transactionId;
        $paymentStatus = $this->getPaymentDetails($transactionId)->status;

        if ($paymentStatus !== PaymentStatus::Success) {
            throw new InvalidStatusForRefundPayment($paymentStatus);
        }

        $builder = CreateRefundRequest::builder();

        try {
            $yooKassaRequest = $builder->build();
            $yooKassaResponse = $this->yooKassaClient->createRefund($yooKassaRequest);

            return $this->responseFactory->createRefundResponse($yooKassaResponse);
        } catch (Exception $exception) {
            $error = $this->errorHandler->handle($exception);

            throw new PaymentGatewayException($error);
        }
    }

    public function isSatisfiedBy(PaymentInterface $payment): bool
    {
        return !($payment instanceof NullPayment) || !$payment->isActive();
    }
}
