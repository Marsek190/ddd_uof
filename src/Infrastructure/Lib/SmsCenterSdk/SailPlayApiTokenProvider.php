<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

interface SailPlayApiTokenProvider
{
    public function getToken(): string;
}
