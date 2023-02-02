<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class BalanceAccountFetchingSailPlayApiClient implements SailPlayApiClientInterface
{
    /**
     * @var int
     */
    private const CRITICAL_ACCOUNT_BALANCE_IN_RUB = 100;

    public function __construct(
        private readonly AccountBalanceFetcherInterface $accountBalanceFetcher,
        private readonly LoggerInterface $logger,
        private readonly SailPlayApiClientInterface $apiClient,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            $balance = $this->accountBalanceFetcher->fetch();

            if ($balance < self::CRITICAL_ACCOUNT_BALANCE_IN_RUB) {
                $this->logger->alert(sprintf('Баланс на счете SailPlay менее %d руб.', $balance));
            }
        } catch (SailPlayApiException $exception) {
            $this->logger->error('Неудалось запросить баланс счета SailPlay.', [
                'error' => $exception->getMessage(),
            ]);
        }

        return $this->apiClient->request($method, $url, $options);
    }
}
