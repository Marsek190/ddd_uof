<?php declare(strict_types=1);

namespace App\Domain\Auth\Token;

interface TokenParserInterface
{
    public function parse(string $token): TokenInterface;
}
