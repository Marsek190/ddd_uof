<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

final class SailPlayLoginResponse extends SailPlayResponse
{
    public string $token = '';

    public function hasError(): bool
    {
        return $this->token === '' && parent::hasError();
    }
}
