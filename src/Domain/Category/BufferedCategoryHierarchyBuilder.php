<?php declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\Aggregate\Category;
use App\Domain\Category\DataProvider\CategoryDataProviderInterface;

final class BufferedCategoryHierarchyBuilder implements CategoryHierarchyBuilder
{
    private ?CategoryHierarchy $hierarchy = null;

    public function __construct(private readonly CategoryDataProviderInterface $categoryDataProvider)
    {
    }

    public function build(): ?CategoryHierarchy
    {
        if ($this->hierarchy !== null) {
            return $this->hierarchy;
        }

        $collection = $this->categoryDataProvider->getAll();

        if ($collection->isEmpty()) {
            return null;
        }

        $collection->sort(function (Category $lft, Category $rgt): int {
            return $lft->getLft() <=> $rgt->getLft();
        });

        $categories = $collection->groupBy(function (Category $category): int {
            return $category->getLevel();
        });

        $hierarchy = $this->getHierarchy(
            $categories,
            $collection->first(),
            Category::ROOT_LEVEL
        );

        $this->hierarchy = $hierarchy[0];

        return $this->hierarchy;
    }

    /**
     * @param array<int, array<int, Category>> $categories
     *
     * @return CategoryHierarchy[]
     */
    private function getHierarchy(array $categories, Category $parent, int $lvl): array
    {
        $hierarchy = [];
        foreach ($categories[$lvl] ?? [] as $key => $category) {
            if ($category->getRgt() > $parent->getRgt()) {
                return $hierarchy;
            }

            if ($parent->getLft() > $category->getLft()) {
                continue;
            }

            $hierarchy[$key] = new CategoryHierarchy($category);
            $nested = $this->getHierarchy($categories, $category, $lvl + 1);
            $hierarchy[$key]->setNested($nested);

            unset($categories[$lvl][$key]);
        }

        return $hierarchy;
    }
}
