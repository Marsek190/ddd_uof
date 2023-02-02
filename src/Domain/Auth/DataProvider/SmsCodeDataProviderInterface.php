<?php

namespace App\Domain\Auth\DataProvider;

use App\Domain\Auth\Aggregate\SmsCode;
use App\Domain\User\ValueObject\Phone;

interface SmsCodeDataProviderInterface
{
    public function getLatestByPhone(Phone $phone): ?SmsCode;
}
