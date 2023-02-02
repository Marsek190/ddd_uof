<?php

namespace App\Infrastructure\Db\Iterator;

use Iterator;
use PDO;
use PDOStatement;
use stdClass;

/**
 * @see https://phpprofi.ru/blogs/post/30
 */
abstract class RowIterator implements Iterator
{
    protected int $key;
    protected false|stdClass $result;
    protected bool $valid;

    public function __construct(protected readonly PDOStatement $stmt)
    {
    }

    public function current(): false|stdClass
    {
        return $this->result;
    }

    public function next(): void
    {
        $this->key++;
        $this->result = $this->stmt->fetch(
            PDO::FETCH_OBJ,
            PDO::FETCH_ORI_ABS,
            $this->key
        );

        if (false === $this->result) {
            $this->valid = false;
        }
    }

    public function key(): int
    {
        return $this->key;
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function rewind(): void
    {
        $this->key = 0;
    }
}
