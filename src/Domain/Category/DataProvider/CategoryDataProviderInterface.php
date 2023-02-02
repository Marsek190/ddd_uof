<?php

namespace App\Domain\Category\DataProvider;

use App\Domain\Category\Categories;

interface CategoryDataProviderInterface
{
    public function getAll(): Categories;
}
