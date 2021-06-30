<?php

declare(strict_types=1);

namespace EtoA\Universe;

class CellPopulation
{
    public int $sx;
    public int $cx;
    public int $sy;
    public int $cy;
    public int $count;

    public function __construct(array $arr)
    {
        $this->sx = (int) $arr['sx'];
        $this->cx = (int) $arr['cx'];
        $this->sy = (int) $arr['sy'];
        $this->cy = (int) $arr['cy'];
        $this->count = (int) $arr['cnt'];
    }
}
