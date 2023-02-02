<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\AuthManagerInterface;
use App\Domain\User\DataProvider\UserDataProviderInterface;
use App\Infrastructure\Db\Entity\User;
use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

final class JwtAuthProvider implements AuthProviderInterface
{
    private const TOKEN_COOKIE_KEY = '__jwtSecret';

    public function __construct(
        private readonly AuthManagerInterface $authManager,
        private readonly JwtProvider $tokenProvider,
        private readonly LoggerInterface $logger,
        private readonly UserDataProviderInterface $userDataProvider,
    ) {
    }

    public function authorize(ServerRequestInterface $request): User
    {
        try {
            $cookie = $request->getCookieParams();
            $token = $this->tokenProvider->parse($cookie[self::TOKEN_COOKIE_KEY] ?? '');
        } catch (RuntimeException) {
            throw new AccessDinedException();
        }

        try {
            $user = $this->authManager->get();
        } catch (Throwable) {
            $user = $this->getUserByToken($token) ?? throw new AccessDinedException();
        }

        $token->addConstraint(new LooseValidAt(new FrozenClock(new DateTimeImmutable())));
        $token->addConstraint(new IdentifiedBy((string)$user->getId()));

        if (!$this->tokenProvider->verify($token)) {
            throw new AccessDinedException();
        }

        $this->authManager->set($user);

        $this->logger->info(
            'Пользователь авторизовался по jwt.',
            [
                (string)$user->getId(),
                (string)$token,
            ]
        );

        return $user;
    }

    private function getUserByToken(Jwt $token): ?User
    {
        if (($userId = $token->getIdentifiedBy()) === null) {
            return null;
        }

        return $this->userDataProvider->get($userId);
    }
}
