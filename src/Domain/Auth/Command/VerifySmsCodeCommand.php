<?php declare(strict_types=1);

namespace App\Domain\Auth\Command;

use App\Domain\CommandInterface;

final class VerifySmsCodeCommand implements CommandInterface
{
    public function __construct(
        public readonly string $code,
        public readonly string $phone,
    ) {
    }
}
