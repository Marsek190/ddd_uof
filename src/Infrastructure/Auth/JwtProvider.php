<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Token\TokenInterface;
use App\Domain\Auth\Token\TokenParserInterface;
use App\Domain\Auth\Token\TokenProviderInterface;
use App\Domain\User\Aggregate\User;
use DateTimeImmutable;
use Exception;
use Lcobucci\JWT\Configuration;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class JwtProvider implements TokenProviderInterface, TokenParserInterface
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function generate(User $user): TokenInterface
    {
        $now = new DateTimeImmutable();
        $signer = $this->configuration->signer();
        $signerKey = $this->configuration->signingKey();

        $builder = $this->configuration->builder()
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            ->expiresAt($now->modify('+1 month'))
            ->issuedBy((string)$user->getId());

        $token = new Jwt($builder->getToken($signer, $signerKey));

        $this->logger->info('Пользователю выдан jwt.', [(string)$token]);

        return $token;
    }

    public function verify(TokenInterface $token): bool
    {
        return $this->configuration
            ->validator()
            ->validate(
                $token->getUnencryptedToken(),
                ...$token->getConstraints()
            );
    }

    /**
     * @return TokenInterface&self
     *
     * @throws RuntimeException
     */
    public function parse(string $token): TokenInterface
    {
        try {
            $token = $this->configuration->parser()->parse($token);

            return new Jwt($token);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                __METHOD__,
                self::class,
                $token,
            ]);

            throw new RuntimeException('Something goes wrong during decoding.');
        }
    }
}
