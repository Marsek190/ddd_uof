<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use App\Infrastructure\Lib\Serializer\SerializerInterface;
use GuzzleHttp\Exception\GuzzleException;

final class HttpAccountBalanceFetcher implements AccountBalanceFetcherInterface
{
    /**
     * @var string
     */
    private const API_ENDPOINT = '/balance.php';

    private string $endpointUrl;

    public function __construct(
        private readonly SailPlayApiClientInterface $apiClient,
        private readonly SerializerInterface $serializer,
        SailPlayApiConfig $config,
    ) {
        $this->endpointUrl = $config->apiUrl . self::API_ENDPOINT;
    }

    /**
     * @return float
     *
     * @throws SailPlayApiException
     */
    public function fetch(): float
    {
        try {
            $jsonBody = (string)$this->apiClient->request('GET', $this->endpointUrl)->getBody();

            /**
             * @var SailPlayBalanceAccountResponse $response
             */
            $response = $this->serializer->deserialize(
                $jsonBody,
                SailPlayBalanceAccountResponse::class,
                'json'
            );

            if ($response->hasError()) {
                throw new SailPlayApiException($response->error);
            }

            return $response->balance;
        } catch (GuzzleException $exception) {
            throw new SailPlayApiException($exception->getMessage());
        }
    }
}
