<?php

namespace EtoA\DTO;

class CryptoDto
{
    private int $sx;
    private int $sy;
    private int $cx;
    private int $cy;
    private int $pos;

    public function getSx(): int
    {
        return $this->sx;
    }

    public function setSx(int $sx): void
    {
        $this->sx = $sx;
    }

    public function getSy(): int
    {
        return $this->sy;
    }

    public function setSy(int $sy): void
    {
        $this->sy = $sy;
    }

    public function getCx(): int
    {
        return $this->cx;
    }

    public function setCx(int $cx): void
    {
        $this->cx = $cx;
    }

    public function getCy(): int
    {
        return $this->cy;
    }

    public function setCy(int $cy): void
    {
        $this->cy = $cy;
    }

    public function getPos(): int
    {
        return $this->pos;
    }

    public function setPos(int $pos): void
    {
        $this->pos = $pos;
    }
}