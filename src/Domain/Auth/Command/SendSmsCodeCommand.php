<?php declare(strict_types=1);

namespace App\Domain\Auth\Command;

use App\Domain\CommandInterface;

final class SendSmsCodeCommand implements CommandInterface
{
    public function __construct(public readonly string $phone)
    {
    }
}
