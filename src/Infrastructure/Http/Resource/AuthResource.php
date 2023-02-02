<?php declare(strict_types=1);

namespace App\Infrastructure\Http\Resource;

use App\Domain\Auth\Token\TokenInterface;
use App\Domain\User\Aggregate\User;
use JetBrains\PhpStorm\ArrayShape;

final class AuthResource extends JsonResource
{
    public function __construct(private readonly User $user, private readonly TokenInterface $token)
    {
    }

    #[ArrayShape([
        'user' => 'array',
        'token' => 'string'
    ])]
    public function getData(): array
    {
        return [
            'user' => [
                'id' => (string)$this->user->getId(),
                'phone' => $this->user->getPhone(),
            ],
            'token' => (string)$this->token,
        ];
    }
}
