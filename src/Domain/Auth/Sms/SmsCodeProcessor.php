<?php declare(strict_types=1);

namespace App\Domain\Auth\Sms;

use App\Domain\Auth\Aggregate\SmsCode;
use App\Domain\Auth\Command\SendSmsCodeCommand;
use App\Domain\Auth\DataProvider\SmsCodeDataProviderInterface;
use App\Domain\EntityManagerInterface;
use App\Domain\User\ValueObject\Phone;
use DomainException;
use Psr\Log\LoggerInterface;

final class SmsCodeProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly SmsCodeGenerator $smsCodeGenerator,
        private readonly SmsCodeDataProviderInterface $smsCodeDataProvider,
        private readonly SmsCodeSenderInterface $smsCodeSender,
    ) {
    }

    /**
     * @throws DomainException
     */
    public function process(SendSmsCodeCommand $command): void
    {
        $phone = new Phone($command->phone);
        $smsCode = $this->getSmsCodeByPhone($phone);
        $verificationCode = $this->smsCodeGenerator->generateSixDigitCode();

        $smsCode->send($verificationCode);
        $this->smsCodeSender->send($phone, $verificationCode);

        $this->entityManager->persists($smsCode);
        $this->entityManager->flush();

        $this->logger->info(
            'Отправлено SMS с кодом подтверждения на мобильный.',
            [
                (string)$smsCode->getPhone(),
                (string)$smsCode->getCode(),
            ]
        );
    }

    private function getSmsCodeByPhone(Phone $phone): SmsCode
    {
        $smsCode = $this->smsCodeDataProvider->getLatestByPhone($phone);

        if ($smsCode !== null && $smsCode->isValid()) {
            return $smsCode;
        }

        return new SmsCode($phone);
    }
}
