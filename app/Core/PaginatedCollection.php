<?php

namespace App\Core;

use ArrayAccess;
use Iterator;

class PaginatedCollection implements ArrayAccess, Iterator
{
    protected $items;
    protected $paginationData;
    private $position = 0;

    public function __construct(array $items, array $paginationData)
    {
        $this->items = $items;
        $this->paginationData = $paginationData;
        $this->position = 0;
    }

    // Métodos da interface ArrayAccess
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    // Métodos da interface Iterator
    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    // Método para renderizar a paginação
    public function pagination($search)
    {
        $pagination = $this->paginationData;
        $search = $search; // Defina conforme necessário
        include __DIR__ . '/../../resources/vendor/pagination.php';
    }
}
