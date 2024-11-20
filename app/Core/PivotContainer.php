<?php

namespace App\Core;

class PivotContainer
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }
}
