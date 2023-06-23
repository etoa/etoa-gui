<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

class EntityCoordinates
{
    public int $sx;
    public int $sy;
    public int $cx;
    public int $cy;
    public int $pos;

    public function __construct(int $sx, int $sy, int $cx, int $cy, int $pos)
    {
        $this->sx = $sx;
        $this->cx = $cx;
        $this->sy = $sy;
        $this->cy = $cy;
        $this->pos = $pos;
    }

    public function toString(): string
    {
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy . " : " . $this->pos;
    }
}
