<?php declare(strict_types=1);

namespace App\Infrastructure;

use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

final class Pagination
{
    /**
     * @var int
     */
    private const DEFAULT_PAGE_SIZE = 10;

    private Collection $items;
    private int $totalItems;
    private int $pagesCount;

    public function __construct(
        Collection $collection,
        private readonly int $currentPage,
        int $pageSize = self::DEFAULT_PAGE_SIZE,
    ) {
        Assert::positiveInteger($currentPage);

        $this->totalItems = $collection->count();
        $this->pagesCount = (int)ceil($this->totalItems / $pageSize);
        $this->items = $collection->forPage($currentPage, $pageSize);
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }
}
