<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\Payment\YooKassa;

use Exception;
use YooKassa\Common\Exceptions\NotFoundException;

final class ErrorHandler
{
    public function handle(Exception $exception): string
    {
        return match (get_class($exception)) {
            NotFoundException::class => 'Ошибка при создании платежа. Платеж не найден.',
            default => $exception->getMessage(),
        };
    }
}
