<?php

namespace App\Domain\Category\Aggregate;

use App\Domain\AggregateRoot;
use Ramsey\Uuid\UuidInterface;

final class Category extends AggregateRoot
{
    public const ROOT_LEVEL = 0;

    public function __construct(
        private readonly UuidInterface $id,
        private readonly string $title,
        private readonly string $alias,
        private readonly int $level,
        private readonly int $lft,
        private readonly int $rgt,
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getLft(): int
    {
        return $this->lft;
    }

    public function getRgt(): int
    {
        return $this->rgt;
    }

    public function getProductsUrl(): string
    {
        return '/catalog/products/category/' . $this->alias . '/';
    }

    public function getCatalogImageUrl(): string
    {
        return '/catalog/category/preview-images/' . $this->alias . '/';
    }
}
