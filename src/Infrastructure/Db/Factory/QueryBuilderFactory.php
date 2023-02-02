<?php

namespace App\Infrastructure\Db\Factory;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class QueryBuilderFactory
{
    /**
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPassword
     * @param string $dbHost
     * @param int $dbPort
     * @param Driver $driver
     */
    public function __construct(
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPassword,
        private readonly string $dbHost,
        private readonly int $dbPort,
        private readonly Driver $driver,
    ) {
    }

    /**
     * @throws Exception
     */
    public function createQueryBuilder(): QueryBuilder
    {
        $connection = DriverManager::getConnection(
            [
                'dbname' => $this->dbName,
                'user' => $this->dbUser,
                'password' => $this->dbPassword,
                'host' => $this->dbHost,
                'port' => $this->dbPort,
                'driver' => $this->driver,
            ],
            new Configuration()
        );

        return new QueryBuilder($connection);
    }
}
