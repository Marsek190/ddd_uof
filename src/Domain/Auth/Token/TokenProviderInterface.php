<?php declare(strict_types=1);

namespace App\Domain\Auth\Token;

use App\Domain\User\Aggregate\User;

interface TokenProviderInterface
{
    public function generate(User $user): TokenInterface;
    public function verify(TokenInterface $token): bool;
}
