<?php

namespace App\Infrastructure\Db;

use PDO;

class DbAdapter
{
    public function __construct(private readonly PDO $driver)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function insertOne(string $table, array $data): void
    {
    }

    /**
     * @param array<array<string, mixed>> $batch
     */
    public function insertBatch(string $table, array $batch): void
    {
    }

    public function deleteOne(string $table, int|string $primaryKey): void
    {
    }

    /**
     * @param int[]|string[] $primaryKeys
     */
    public function deleteBatch(string $table, array $primaryKeys): void
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateOne(string $table, int|string $primaryKey, array $data): void
    {
    }

    /**
     * @param array<array<string, mixed>> $where
     * @param array<array<string, mixed>> $replacements
     */
    public function updateBatch(string $table, array $where, array $replacements): void
    {
    }

    public function getDriver(): PDO
    {
        return $this->driver;
    }
}
