<?php declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use App\SharedKernel\Validation\Assert;

final class Phone
{
    public const REGEX_PATTERN = '/^7\d{10}$/';

    public function __construct(private readonly string $value)
    {
        Assert::regex($value, self::REGEX_PATTERN, 'Укажите телефон в правильном формате.');
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
