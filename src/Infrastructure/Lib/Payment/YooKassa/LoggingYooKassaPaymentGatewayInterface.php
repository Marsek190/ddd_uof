<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\Payment\YooKassa;

use App\Domain\Payment\InvalidStatusForPaymentCancellation;
use App\Domain\Payment\PaymentInterface;
use App\Domain\Payment\PaymentGatewayException;
use App\Domain\Payment\PaymentGatewayInterface;
use App\Domain\Payment\PaymentRequest;
use App\Domain\Payment\PaymentResponse;
use App\Domain\Payment\RefundPaymentRequest;
use App\Domain\Payment\RefundPaymentResponse;
use Psr\Log\LoggerInterface;

final class LoggingYooKassaPaymentGatewayInterface implements PaymentGatewayInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly YooKassaPaymentGateway $yooKassaPaymentGateway
    ) {
    }

    /**
     * @throws PaymentGatewayException
     */
    public function createPayment(PaymentRequest $request): PaymentResponse
    {
        $context = [
            'tx_id' => (string)$request->transactionId,
            'user_id' => (string)$request->user->getId(),
        ];

        $this->logger->info('Запрос к YooKassa на создание оплаты.', $context);

        try {
            $response = $this->yooKassaPaymentGateway->createPayment($request);
            $transactionDetails = $response->transactionDetails;

            $this->logger->info(
                'Запрос к YooKassa на создание оплаты успешно выполнен.',
                array_replace($context, ['tx_details' => $transactionDetails])
            );

            return $response;
        } catch (PaymentGatewayException $exception) {
            $this->logger->error(
                'Запрос к YooKassa на создание оплаты завершился ошибкой.',
                array_replace($context, ['error' => $exception->getMessage()])
            );

            throw $exception;
        }
    }

    /**
     * @throws PaymentGatewayException
     */
    public function getPaymentDetails(string $transactionId): PaymentResponse
    {
        $context = [
            'tx_id' => $transactionId,
        ];

        $this->logger->info('Запрос к YooKassa на получение данных об оплате.', $context);

        try {
            $response = $this->yooKassaPaymentGateway->getPaymentDetails($transactionId);
            $transactionDetails = $response->transactionDetails;

            $this->logger->info(
                'Запрос к YooKassa на получение данных об оплате успешно выполнен.',
                [...$context, 'tx_details' => $transactionDetails]
            );

            return $response;
        } catch (PaymentGatewayException $exception) {
            $this->logger->error(
                'Запрос к YooKassa на получение данных об оплате завершился ошибкой.',
                [...$context, 'error' => $exception->getMessage()]
            );

            throw $exception;
        }
    }

    /**
     * @throws PaymentGatewayException
     * @throws InvalidStatusForPaymentCancellation
     */
    public function cancelPayment(string $transactionId): PaymentResponse
    {
        $context = [
            'tx_id' => $transactionId,
        ];

        $this->logger->info('Запрос к YooKassa на отмену платежа.', $context);

        try {
            $response = $this->yooKassaPaymentGateway->cancelPayment($transactionId);
            $transactionDetails = $response->transactionDetails;

            $this->logger->info(
                'Запрос к YooKassa на отмену оплаты успешно выполнен.',
                [...$context, 'tx_details' => $transactionDetails]
            );

            return $response;
        } catch (PaymentGatewayException $exception) {
            $this->logger->error(
                'Запрос к YooKassa на отмену оплаты завершился ошибкой.',
                [...$context, 'error' => $exception->getMessage()]
            );

            throw $exception;
        } catch (InvalidStatusForPaymentCancellation $exception) {
            $this->logger->error($exception->getMessage(), $context);

            throw $exception;
        }
    }

    /**
     * @throws PaymentGatewayException
     */
    public function refundPayment(RefundPaymentRequest $request): RefundPaymentResponse
    {
        $context = [
            'tx_id' => $request->transactionId,
        ];

        $this->logger->info('Запрос к YooKassa на возврат средств по платежу.', $context);

        try {
            $response = $this->yooKassaPaymentGateway->refundPayment($request);
            $transactionDetails = $response->transactionDetails;

            $this->logger->info(
                'Запрос к YooKassa на возврат средств по платежу успешно выполнен.',
                [...$context, 'tx_details' => $transactionDetails]
            );

            return $response;
        } catch (PaymentGatewayException $exception) {
            $this->logger->error('', []);

            throw $exception;
        }
    }

    public function isSatisfiedBy(PaymentInterface $payment): bool
    {
        return $this->yooKassaPaymentGateway->isSatisfiedBy($payment);
    }
}
