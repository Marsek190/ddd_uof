<?php declare(strict_types=1);

namespace App\Infrastructure\Lib\SmsCenterSdk;

use App\Domain\Auth\Sms\SmsCodeSenderInterface;
use App\Domain\Auth\ValueObject\VerificationCodeInterface;
use App\Domain\User\ValueObject\Phone;
use App\Infrastructure\Lib\Serializer\SerializerInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

final class SailPlaySmsCodeSender implements SmsCodeSenderInterface
{
    /**
     * @var string
     */
    private const API_ENDPOINT = '/send.php';

    private string $endpointUrl;

    public function __construct(
        private readonly SailPlayApiClient $httpClient,
        private readonly SailPlayApiTokenProvider $tokenProvider,
        SailPlayApiConfig $config,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
    ) {
        $this->endpointUrl = $config->apiUrl . self::API_ENDPOINT;
    }

    public function send(Phone $phone, VerificationCodeInterface $code): void
    {
        try {
            $options = [
                'query' => [
                    'mes' => sprintf(self::TEXT_FOR_SMS_RECIPIENT, (string)$code),
                    'phones' => (string)$phone,
                ],
                'headers' => [
                    'X-Auth-TokenInterface' => $this->tokenProvider->getToken(),
                ],
            ];

            $jsonBody = (string)$this->httpClient->request('GET', $this->endpointUrl, $options)->getBody();

            /**
             * @var SailPlayResponse $response
             */
            $response = $this->serializer->deserialize($jsonBody, SailPlayResponse::class, 'json');

            if (!$response->hasError()) {
                return;
            }

            $this->logger->error('Запрос к апи SailPlay завершился ошибкой.', [
                'error' => $response->error,
            ]);
        } catch (GuzzleException) {
        }

        throw new SailPlayApiException('При отправке SMS возникла ошибка.');
    }
}
