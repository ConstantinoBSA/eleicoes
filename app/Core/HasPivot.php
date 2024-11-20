<?php

namespace App\Core;

trait HasPivot
{
    private $pivot;

    public function setPivot(PivotContainer $pivot)
    {
        $this->pivot = $pivot;
    }

    public function getPivot()
    {
        return $this->pivot;
    }
}
