<?php declare(strict_types=1);

namespace App\Domain\Auth\Sms;

use App\Domain\Auth\ValueObject\VerificationCodeInterface;
use App\Domain\User\ValueObject\Phone;

interface SmsCodeSenderInterface
{
    /**
     * @var string
     */
    public const TEXT_FOR_SMS_RECIPIENT = 'Ваш код подтверждения: %s.';

    public function send(Phone $phone, VerificationCodeInterface $code): void;
}
