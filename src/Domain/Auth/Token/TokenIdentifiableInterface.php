<?php declare(strict_types=1);

namespace App\Domain\Auth\Token;

use Ramsey\Uuid\UuidInterface;

interface TokenIdentifiableInterface
{
    public function getIdentifiedBy(): ?UuidInterface;
}
