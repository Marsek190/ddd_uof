<?php declare(strict_types=1);

namespace App\SharedKernel;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

final class PhrasePluralizer
{
    /**
     * @param string[] $endings
     *
     * @throws InvalidArgumentException
     */
    public static function pluralize(int $number, array $endings): string
    {
        Assert::greaterThanEq($number, 0);
        Assert::notEmpty($endings);

        $cases = [2, 0, 1, 1, 1, 2];

        if ($number % 100 > 4 && $number % 100 < 20) {
            return sprintf($endings[2], $number);
        }

        return sprintf($endings[$cases[min($number % 10, 5)]], $number);
    }
}
