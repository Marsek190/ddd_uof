<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

class SailPlayResponse
{
    public ?string $error;
    public ?string $errorCode;

    public function hasError(): bool
    {
        return $this->error !== null;
    }
}
