<?php

namespace App\Infrastructure\Db\Entity;

use InvalidArgumentException;

interface EntityInterface
{
    public function __construct();

    public static function getTable(): string;
    public function toArray(): array;

    /**
     * @throws InvalidArgumentException
     */
    public static function hydrate(array $data, array $requiredFields = []): static;
}
