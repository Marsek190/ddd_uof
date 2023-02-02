<?php declare(strict_types=1);

namespace App\Infrastructure;

final class SmsResponse
{
    public string $status = '';
    public ?string $error = null;

    public function hasError(): bool
    {
        return $this->error !== null;
    }
}
