<?php declare(strict_types=1);

namespace App\Domain\Auth\Token;

interface TokenInterface
{
    public function isExpired(): bool;
    public function __toString(): string;
}
