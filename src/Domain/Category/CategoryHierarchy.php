<?php declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\Aggregate\Category;

final class CategoryHierarchy
{
    /**
     * @var CategoryHierarchy[]|null
     */
    private ?array $nested = null;

    private int $nestedCount = 0;

    public function __construct(private readonly Category $parent)
    {
    }

    public function getParent(): Category
    {
        return $this->parent;
    }

    public function getNested(): ?array
    {
        return $this->nested;
    }

    public function getNestedCount(): int
    {
        return $this->nestedCount;
    }

    /**
     * @param CategoryHierarchy[] $nested
     */
    public function setNested(array $nested): void
    {
        $this->nested = $nested;
        $this->nestedCount += array_reduce(
            $nested,
            function (int $nestedCount, CategoryHierarchy $node): int {
                return $nestedCount + $node->getNestedCount();
            },
            initial: 0
        );
    }
}
