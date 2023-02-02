<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

final class SailPlayApiClient implements SailPlayApiClientInterface
{
    /**
     * @var int
     */
    private const JSON_RESPONSE_FORMAT = 3;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly SailPlayApiConfig $config,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            $options = array_replace_recursive(
                $options,
                [
                    'query' => [
                        'login' => $this->config->login,
                        'psw' => $this->config->password,
                        'fmt' => self::JSON_RESPONSE_FORMAT,
                        'call' => 1,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'timeout' => $this->config->timeout,
                ],
            );

            return $this->httpClient->request($method, $url, $options);
        } catch (GuzzleException $exception) {
            $this->logger->error(
                sprintf('Апи SailPlay ответил ошибкой: "%s".', $exception->getMessage()),
                [
                    self::class,
                    $method,
                    $url,
                    $options,
                ]
            );

            throw $exception;
        }
    }
}
