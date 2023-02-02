<?php

namespace App\Domain\Auth\ValueObject;

use App\SharedKernel\Validation\Assert;

final class SixDigitVerificationCode implements VerificationCodeInterface
{
    /**
     * @var int
     */
    private const CODE_LENGTH = 6;

    private function __construct(private readonly string $value)
    {
    }

    public static function generate(): self
    {
        $verificationCode = '';
        while (strlen($verificationCode) < self::CODE_LENGTH) {
            $verificationCode .= mt_rand(0, 9);
        }

        return new self($verificationCode);
    }

    public static function fromString(string $value): self
    {
        Assert::regex($value, '/\d{6}/', 'Неверный формат кода.');

        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(VerificationCodeInterface $code): bool
    {
        return $this->value === (string)$code;
    }
}
