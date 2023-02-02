<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use App\Infrastructure\Lib\Serializer\SerializerInterface;
use GuzzleHttp\Exception\GuzzleException;

final class HttpSailPlayApiTokenProvider implements SailPlayApiTokenProvider
{
    /**
     * @var string
     */
    private const API_ENDPOINT = '/token.php';

    private string $endpointUrl;

    public function __construct(
        private readonly SailPlayApiClient $httpClient,
        SailPlayApiConfig $config,
        private readonly SerializerInterface $serializer,
    ) {
        $this->endpointUrl = $config->apiUrl . self::API_ENDPOINT;
    }

    public function getToken(): string
    {
        try {
            $jsonBody = (string)$this->httpClient->request('GET', $this->endpointUrl)->getBody();

            /**
             * @var SailPlayLoginResponse $response
             */
            $response = $this->serializer->deserialize($jsonBody, SailPlayLoginResponse::class, 'json');

            if (!$response->hasError()) {
                return $response->token;
            }
        } catch (GuzzleException) {
        }

        throw new SailPlayApiException('Не удалось получить токен.');
    }
}
