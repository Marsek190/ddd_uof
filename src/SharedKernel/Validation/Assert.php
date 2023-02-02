<?php

namespace App\SharedKernel\Validation;

use InvalidArgumentException;

final class Assert extends \Webmozart\Assert\Assert
{
    /**
     * @param string[] $keys
     *
     * @throws InvalidArgumentException
     */
    public static function allKeysArePresentInArray(array $keys, array $data, string $message = ''): void
    {
        foreach ($keys as $key) {
            self::keyExists(
                array: $data,
                key: $key,
                message: $message ?: sprintf('The key "%s" does not exist', $key)
            );
        }
    }
}
