<?php

namespace EtoA\DTO;

use EtoA\Entity\Missile;

class MissileLaunchDto
{
    private Missile $missile;
    private int $count;

    public function getMissile(): Missile
    {
        return $this->missile;
    }

    public function setMissile(Missile $missile): void
    {
        $this->missile = $missile;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }


}