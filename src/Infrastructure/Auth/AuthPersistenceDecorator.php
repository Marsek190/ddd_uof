<?php declare(strict_types=1);

namespace App\Infrastructure\Auth;

use App\Domain\EntityManagerInterface;
use App\Domain\User\Aggregate\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthPersistenceDecorator implements AuthProviderInterface
{
    public function __construct(
        private readonly AuthProviderInterface $authProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function authorize(ServerRequestInterface $request): User
    {
        $user = $this->authProvider->authorize($request);

        $this->entityManager->transactional(function () use ($user): void {
            $user->authorize();

            foreach ($user->popEvents() as $event) {
                $this->dispatcher->dispatch($event);
            }
        });

        return $user;
    }
}
