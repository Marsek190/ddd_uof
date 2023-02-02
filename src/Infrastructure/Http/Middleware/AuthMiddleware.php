<?php

namespace App\Infrastructure\Http\Middleware;

use App\Domain\User\Aggregate\User;
use App\Infrastructure\Auth\AccessDinedException;
use App\Infrastructure\Auth\AuthProviderInterface;
use App\Infrastructure\Auth\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly AuthProviderInterface $authProvider)
    {
    }

    /**
     * @throws AccessDinedException
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authProvider->authorize($request);
        $request = $request->withAttribute(User::class, $user);

        return $handler->handle($request);
    }
}
