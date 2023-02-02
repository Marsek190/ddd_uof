<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Command\VerifySmsCodeCommand;
use App\Domain\Auth\Sms\SmsCodeVerifier;
use App\Domain\Auth\ValueObject\SixDigitVerificationCode;
use App\Domain\User\Aggregate\User;
use App\Domain\User\DataProvider\UserDataProviderInterface;
use App\Domain\User\ValueObject\Phone;
use App\SharedKernel\Validation\ValidatorInterface;
use JsonException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

final class SmsCodeAuthProvider implements AuthProviderInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SmsCodeVerifier $smsCodeVerifier,
        private readonly UserDataProviderInterface $userDataProvider,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function authorize(ServerRequestInterface $request): User
    {
        /** @var array $data */
        $data = json_decode(
            json: (string)$request->getBody(),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );

        $this->validator->validate($data, [
            'phone' => ['required', 'string', 'regex:'. Phone::REGEX_PATTERN],
            'code' => ['required', 'string', 'min:1', 'max:6'],
        ]);

        $command = new VerifySmsCodeCommand(...$data);
        $verificationCode = SixDigitVerificationCode::fromString($command->code);
        $phone = new Phone($command->phone);

        $this->smsCodeVerifier->verify($phone, $verificationCode);

        $user = $this->userDataProvider->getByPhone($phone) ?? new User(Uuid::uuid4(), $phone);

        $this->logger->info(
            'Пользователь авторизован через SMS код.',
            [
                'userId' => (string)$user->getId(),
            ]
        );

        return $user;
    }
}
