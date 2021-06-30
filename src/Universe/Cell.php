<?php

declare(strict_types=1);

namespace EtoA\Universe;

class Cell
{
    public int $id;
    public int $sx;
    public int $cx;
    public int $sy;
    public int $cy;

    public function __construct(array $arr)
    {
        $this->id = (int) $arr['id'];
        $this->sx = (int) $arr['sx'];
        $this->cx = (int) $arr['cx'];
        $this->sy = (int) $arr['sy'];
        $this->cy = (int) $arr['cy'];
    }

    public function toString(): string
    {
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy;
    }

    /**
     * @return array<int>
     */
    public function getAbsoluteCoordinates(int $numberOfCellsX, int $numberOfCellsY): array
    {
        $x = (($this->sx - 1) * $numberOfCellsX) + $this->cx;
        $y = (($this->sy - 1) * $numberOfCellsY) + $this->cy;

        return [$x, $y];
    }
}
