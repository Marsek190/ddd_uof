<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class CachedSailPlayApiTokenProvider implements SailPlayApiTokenProvider
{
    /**
     * @var string
     */
    private const API_TOKEN_CACHE_KEY = '__SailPlayApiToken';

    public function __construct(
        private CacheInterface $cache,
        private SailPlayApiTokenProvider $tokenProvider,
        private int $ttl,
    ) {
    }

    public function getToken(): string
    {
        $token = null;
        try {
            if ($this->cache->has(self::API_TOKEN_CACHE_KEY)) {
                return $this->cache->get(self::API_TOKEN_CACHE_KEY);
            }

            $token = $this->tokenProvider->getToken();
            $this->cache->set(self::API_TOKEN_CACHE_KEY, $token, $this->ttl);

            return $token;
        } catch (InvalidArgumentException) {
        }

        return $token ?? $this->tokenProvider->getToken();
    }
}
