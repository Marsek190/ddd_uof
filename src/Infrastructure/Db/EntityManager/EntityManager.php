<?php

namespace App\Infrastructure\Db\EntityManager;

use App\Domain\AggregateRoot;
use App\Domain\EntityManagerInterface;
use App\Domain\Exception\TransactionException;
use App\Infrastructure\Db\DbAdapter;
use App\Infrastructure\Db\IdentityMap;
use App\Infrastructure\Db\Entity\EntityInterface;
use App\SharedKernel\HydratorInterface;
use InvalidArgumentException;
use PDOException;

abstract class EntityManager implements EntityManagerInterface
{
    /**
     * @var array<string, AggregateRoot|EntityInterface>
     */
    private array $instances = [];

    protected string $entityClassName = '';

    public function __construct(
        private readonly HydratorInterface $hydrator,
        private readonly IdentityMap $identityMap,
        protected readonly DbAdapter $dbAdapter,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public final function persists(AggregateRoot $aggregateRoot): void
    {
        if ($this->isSatisfiedBy($aggregateRoot) === false) {
            throw new InvalidArgumentException("{$aggregateRoot::class} is not allowed.");
        }

        $uuid = (string)$aggregateRoot->getId();

        if (!($aggregateRoot instanceof EntityInterface)) {
            $this->instances[$uuid] = $this->hydrator->hydrate(
                $this->entityClassName,
                $aggregateRoot->toArray()
            );
        } else {
            $this->instances[$uuid] = $aggregateRoot;
        }
    }

    public function remove(AggregateRoot $aggregateRoot): void
    {
        if ($this->isSatisfiedBy($aggregateRoot) === false) {
            throw new InvalidArgumentException("{$aggregateRoot::class} is not allowed.");
        }

        /** @var AggregateRoot|EntityInterface $aggregateRoot */

        $this->dbAdapter->deleteOne($aggregateRoot::getTable(), (string)$aggregateRoot->getId());
    }

    public final function flush(): void
    {
        $callback = function () {
            foreach ($this->instances as $instance) {
                $uuid = $instance->getId();

                if ($this->identityMap->has($uuid) === false) {
                    $this->insertAggregateRoot($instance);
                } else {
                    /** @var AggregateRoot $initial */
                    $initial = $this->identityMap->get($uuid);

                    $this->updateAggregateRoot($initial, $instance);
                }
            }

            $this->instances = [];
        };

        $this->transactional($callback);
    }

    public function transactional(callable $callback): void
    {
        $this->dbAdapter->getDriver()->beginTransaction();

        try {
            // do stuff
            $this->flush();
            call_user_func($callback);

            $this->dbAdapter->getDriver()->commit();
        } catch (PDOException $e) {
            $this->dbAdapter->getDriver()->rollBack();

            throw new TransactionException($e->getMessage());
        }
    }

    protected abstract function isSatisfiedBy(AggregateRoot $aggregateRoot): bool;

    protected abstract function insertAggregateRoot(AggregateRoot $aggregateRoot): void;

    protected abstract function updateAggregateRoot(AggregateRoot $initial, AggregateRoot $updated): void;
}