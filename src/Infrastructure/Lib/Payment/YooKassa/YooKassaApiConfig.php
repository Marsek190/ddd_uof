<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\Payment\YooKassa;

final class YooKassaApiConfig
{
    /**
     * @var int
     */
    private const DEFAULT_TIMEOUT = 2;

    public function __construct(
        public readonly string $shopId,
        public readonly string $apiToken,
        public readonly int $timeout = self::DEFAULT_TIMEOUT,
    ) {
    }
}
