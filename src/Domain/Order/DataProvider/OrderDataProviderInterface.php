<?php /** @noinspection PhpDocSignatureIsNotCompleteInspection */

namespace App\Domain\Order\DataProvider;

use App\Domain\CommandInterface;
use App\Domain\Order\Aggregate\Order;
use App\Domain\QueryInterface;
use App\Domain\ReadModelInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

interface OrderDataProviderInterface
{
    /**
     * Use this one in case, when you need filter orders by many params and render they on client side
     *
     * @return Collection<ReadModelInterface>
     * @throws InvalidArgumentException
     */
    public function getByQuery(QueryInterface $query): Collection;

    /**
     * Use this one in case, when you need take filled aggregate by the business logic
     *
     * @throws InvalidArgumentException
     */
    public function getByCommand(CommandInterface $command): ?Order;
}
