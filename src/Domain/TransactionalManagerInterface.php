<?php declare(strict_types=1);

namespace App\Domain;

use App\Domain\Exception\TransactionException;

interface TransactionalManagerInterface
{
    /**
     * @throws TransactionException
     */
    public function transactional(callable $callback): void;
}
