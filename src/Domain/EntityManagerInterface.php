<?php declare(strict_types=1);

namespace App\Domain;

interface EntityManagerInterface extends TransactionalManagerInterface
{
    public function remove(AggregateRoot $aggregateRoot): void;
    public function persists(AggregateRoot $aggregateRoot): void;
    public function flush(): void;
}
