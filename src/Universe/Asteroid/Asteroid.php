<?php

declare(strict_types=1);

namespace EtoA\Universe\Asteroid;

use EtoA\Core\ObjectWithImage;
use EtoA\Fleet\FleetAction;
use EtoA\Universe\Entity\AbstractEntity;

class Asteroid extends AbstractEntity implements ObjectWithImage
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

    public function getImagePath(string $type = ""): string
    {
        $numImages = 5;
        $r = ($this->id % $numImages) + 1;
        return ObjectWithImage::BASE_PATH . "/asteroids/asteroids" . $r . "_small.png";
    }

    public function getEntityCodeString(): string
    {
        return "Asteroidenfeld";
    }

    public function getAllowedFleetActions(): array
    {
        return [FleetAction::COLLECT_METAL, FleetAction::ANALYZE, FleetAction::FLIGHT, FleetAction::EXPLORE];
    }
}
