<?php declare(strict_types=1);

namespace App\Domain\Category;

interface CategoryHierarchyBuilder
{
    public function build(): ?CategoryHierarchy;
}
