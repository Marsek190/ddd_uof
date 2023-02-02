<?php

namespace App\Infrastructure\Db\Factory;

use PDO;

/** @noinspection PhpUnused */
class PDOFactory
{
    /**
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbHost
     * @param int $dbPort
     * @param string $platform
     */
    public function __construct(
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPassword,
        private readonly string $dbHost,
        private readonly int $dbPort,
        private readonly string $platform = 'mysql',
    ) {
    }

    /** @noinspection PhpUnused */
    public function createPDO(): PDO
    {
        $dsn = sprintf(
            '%s:dbname=%s;host=%s;port=%d',
            $this->platform,
            $this->dbName,
            $this->dbHost,
            $this->dbPort,
        );

        return new PDO(dsn: $dsn, username: $this->dbUser, password: $this->dbPassword);
    }
}
