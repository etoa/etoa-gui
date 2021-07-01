<?php

declare(strict_types=1);

namespace EtoA\Universe\Asteroids;

class Asteroid
{
    public int $id;
    public int $resMetal;
    public int $resCrystal;
    public int $resPlastic;
    public int $resFuel;
    public int $resFood;
    public int $resPower;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->resMetal = (int) $data['res_metal'];
        $this->resCrystal = (int) $data['res_crystal'];
        $this->resPlastic = (int) $data['res_plastic'];
        $this->resFuel = (int) $data['res_fuel'];
        $this->resFood = (int) $data['res_food'];
        $this->resPower = (int) $data['res_power'];
    }
}
