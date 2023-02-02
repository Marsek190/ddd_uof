<?php

namespace App\Domain\Auth\Sms;

use App\Domain\Auth\DataProvider\SmsCodeDataProviderInterface;
use App\Domain\Auth\Exception\VerificationCodeIsWrongException;
use App\Domain\Auth\ValueObject\VerificationCodeInterface;
use App\Domain\EntityManagerInterface;
use App\Domain\User\ValueObject\Phone;
use DomainException;

final class SmsCodeVerifier
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SmsCodeDataProviderInterface $smsCodeDataProvider,
    ) {
    }

    public function verify(Phone $phone, VerificationCodeInterface $verificationCode): void
    {
        $smsCode = $this->smsCodeDataProvider->getLatestByPhone($phone);

        if ($smsCode === null) {
            throw new DomainException('Смс-код не найден.');
        }

        try {
            $smsCode->verify($verificationCode);
        } catch (VerificationCodeIsWrongException $exception) {
            $this->entityManager->persists($smsCode);
            $this->entityManager->flush();

            throw $exception;
        }

        $this->entityManager->remove($smsCode);
    }
}
