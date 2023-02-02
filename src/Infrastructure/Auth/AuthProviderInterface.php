<?php

namespace App\Infrastructure\Auth;

use App\Domain\User\Aggregate\User;
use Psr\Http\Message\ServerRequestInterface;

interface AuthProviderInterface
{
    /**
     * @throws AccessDinedException
     * @throws ForbiddenException
     */
    public function authorize(ServerRequestInterface $request): User;
}
