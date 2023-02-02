<?php declare(strict_types=1);

namespace App\Domain\Auth\Sms;

use App\Domain\Auth\ValueObject\VerificationCodeInterface;
use App\Domain\User\ValueObject\Phone;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class SwitchableSmsCodeSender implements SmsCodeSenderInterface
{
    /**
     * @var int
     */
    private const TTL = 60 * 15; // 15 minutes

    /**
     * @var string
     */
    private const SMS_GATEWAY_CACHE_KEY = '';

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SmsCodeSenderInterface $mainGateway, // cheapest and bugs-oriented
        private readonly SmsCodeSenderInterface $backupGateway, // high-sustainable & high-performance, but too expensive
        private readonly LoggerInterface $logger,
        private readonly int $ttl = self::TTL,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function send(Phone $phone, VerificationCodeInterface $code): void
    {
        if ($this->cache->has(self::SMS_GATEWAY_CACHE_KEY)) {
            $this->backupGateway->send($phone, $code);

            return;
        }

        $counter = new SmsCodeAttemptsCounter();

        while ($counter->canSend()) {
            try {
                $this->mainGateway->send($phone, $code);

                if ($counter->getAttempts() > 0) {
                    $info = sprintf(
                        'Отправка SMS через основной шлюз. Совершено попыток: %d.',
                        $counter->getAttempts()
                    );

                    $this->logger->info($info);
                }

                return;
            } catch (Exception) {
                $counter->addAttempt();
            }
        }

        $this->logger->info('Переключение на запасный шлюз.');
        $this->backupGateway->send($phone, $code);
        $this->cache->set(self::SMS_GATEWAY_CACHE_KEY, 1, $this->ttl);
    }
}
