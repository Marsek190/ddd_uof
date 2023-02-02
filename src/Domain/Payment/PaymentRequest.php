<?php declare(strict_types=1);

namespace App\Domain\Payment;

use App\Domain\User\Aggregate\User;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

final class PaymentRequest
{
    public function __construct(
        public readonly UuidInterface $transactionId,
        public readonly PaymentInterface $payment,
        public readonly User $user,
        public readonly int $transactionAmount,
        public readonly ?string $redirectUrlIfTransactionSucceeds,
    ) {
        Assert::true($payment->isActive(), 'Выбран неактивный способ оплаты.');
        Assert::greaterThan(
            $transactionAmount,
            0,
            'Сумма транзакции должна быть больше 0.'
        );
    }
}
