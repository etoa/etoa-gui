<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

class Entity
{
    public int $id;
    public int $cellId;
    public string $code;
    public int $sx;
    public int $cx;
    public int $sy;
    public int $cy;
    public int $pos;

    public function __construct(array $arr)
    {
        $this->id = (int) $arr['id'];
        $this->cellId = (int) $arr['cid'];
        $this->code = $arr['code'];
        $this->sx = (int) $arr['sx'];
        $this->cx = (int) $arr['cx'];
        $this->sy = (int) $arr['sy'];
        $this->cy = (int) $arr['cy'];
        $this->pos = (int) $arr['pos'];
    }

    public function toString(): string
    {
        return $this->codeString() . ' ' . $this->coordinatesString();
    }

    public function getCoordinates(): EntityCoordinates
    {
        return new EntityCoordinates($this->sx, $this->sy, $this->cx, $this->cy, $this->pos);
    }

    public function coordinatesString(): string
    {
        return $this->sx . "/" . $this->sy . " : " . $this->cx . "/" . $this->cy . " : " . $this->pos;
    }

    public function codeString(): string
    {
        switch ($this->code) {
            case EntityType::ALLIANCE_MARKET:
                return 'Allianz';
            case EntityType::ASTEROID:
                return 'Asteroidenfeld';
            case EntityType::EMPTY_SPACE:
                return 'Leerer Raum';
            case EntityType::MARKET:
                return 'Marktplatz';
            case EntityType::NEBULA:
                return 'Interstellarer Gasnebel';
            case EntityType::PLANET:
                return 'Planet';
            case EntityType::STAR:
                return 'Stern';
            case EntityType::WORMHOLE:
                return 'Wurmloch';
            case EntityType::UNEXPLORED:
                return 'Unerforschte Raumzelle!';
        }

        return 'Unbekannter Raum';
    }
}
