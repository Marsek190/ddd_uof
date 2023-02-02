<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Token\TokenInterface;
use App\Domain\Auth\Token\TokenIdentifiableInterface;
use DateTimeImmutable;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Ramsey\Uuid\Uuid;

final class Jwt implements TokenInterface, TokenIdentifiableInterface
{
    /**
     * @var Constraint[]
     */
    private array $constraints = [];

    public function __construct(private readonly UnencryptedToken $unencryptedToken)
    {
    }

    public function getIdentifiedBy(): ?Uuid
    {
        return $this->unencryptedToken->claims()->get(RegisteredClaims::ID);
    }

    public function isExpired(): bool
    {
        return $this->unencryptedToken->isExpired(new DateTimeImmutable());
    }

    public function __toString(): string
    {
        return $this->unencryptedToken->toString();
    }

    public function getUnencryptedToken(): UnencryptedToken
    {
        return $this->unencryptedToken;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function addConstraint(Constraint $constraint): void
    {
        $this->constraints[] = $constraint;
    }
}
