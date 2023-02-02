<?php

namespace App\Domain\Auth\Sms;

use App\Domain\Auth\ValueObject\SixDigitVerificationCode;
use App\Domain\Auth\ValueObject\VerificationCodeInterface;

final class SmsCodeGenerator
{
    public function generateSixDigitCode(): VerificationCodeInterface
    {
        return SixDigitVerificationCode::generate();
    }
}
