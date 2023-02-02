<?php declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\Aggregate\Category;
use ArrayObject;
use Webmozart\Assert\Assert;

final class Categories extends ArrayObject
{
    /**
     * @param int $key
     */
    public function offsetGet($key): ?Category
    {
        if ($this->offsetExists($key)) {
            return null;
        }

        return parent::offsetGet($key);
    }

    /**
     * @param int $key
     * @param Category $value
     */
    public function offsetSet($key, $value): void
    {
        Assert::isInstanceOf($value, Category::class);

        parent::offsetSet($key, $value);
    }

    public function first(): ?Category
    {
        if ($this->count() === 0) {
            return null;
        }

        return $this->offsetGet(0);
    }

    public function sort(callable $callback): void
    {
        $categories = iterator_to_array($this);
        usort($categories, $callback);

        $this->exchangeArray($categories);
    }

    public function groupBy(callable $aggregateBy): array
    {
        $groups = [];

        /**
         * @var Category $category
         */
        foreach ($this as $category) {
            $key = call_user_func($aggregateBy, $category);

            $groups[$key][] = $category;
        }

        return $groups;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
