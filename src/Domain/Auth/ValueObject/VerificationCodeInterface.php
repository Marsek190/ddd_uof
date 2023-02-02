<?php

namespace App\Domain\Auth\ValueObject;

interface VerificationCodeInterface
{
    public function getValue(): string;
    public function equals(self $code): bool;
    public function __toString(): string;
}
