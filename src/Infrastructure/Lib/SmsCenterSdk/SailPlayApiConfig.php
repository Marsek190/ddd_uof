<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

final class SailPlayApiConfig
{
    /**
     * @var int
     */
    private const DEFAULT_TIMEOUT = 2;

    public function __construct(
        public readonly string $apiUrl,
        public readonly string $login,
        public readonly string $password,
        public readonly int $timeout = self::DEFAULT_TIMEOUT,
    ) {
    }
}
